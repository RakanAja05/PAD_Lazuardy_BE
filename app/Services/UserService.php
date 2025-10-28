<?php

namespace App\Services;

use App\Enums\RoleEnum;
use App\Enums\TutorStatusEnum;
use App\Models\Student;
use App\Models\Tutor;
use App\Models\User;
use Exception;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class UserService
{
    public function convertAddressToJson($data)
    {
        return json_encode(
            [
            "province" => $data["province"],
            "regency" => $data["regency"],
            "district" => $data["district"],
            "subdistrict" => $data["subdistrict"],
            "street" => $data["street"],
            ]
        );
    }

    public function showUserProfile(User $query)
    {
        $address = $query->home_address;
        $data = [
            'name' => $query->name,
            'email' => $query->email,
            'telephone_number' => $query->telephone_number,
            'profile_photo_url' => $query->profile_photo_url,
            'gender' => $query->gender,
            'date_of_birth' => $query->date_of_birth,
            'religion' => $query->religion,

            'province' => $address['province']?? null,
            'city' => $address['regency']?? null,
            'district' => $address['district']?? null,
            'subdistrict' => $address['subdistrict']?? null,
            'street' => $address['street']?? null,
        ];

        return $data;
    }

    public function updateBiodataRegistration(User $user, Collection $userData, Collection $addressData){
        DB::beginTransaction();
        try 
        {
            $updated = $user->update([
                'name' => $userData['name'],
                'role' => RoleEnum::STUDENT->value,
                'gender' => $userData['gender'],
                'date_of_birth' => $userData['date_of_birth'],
                'telephone_number' => $userData['telephone_number'],
                'home_address' => $addressData,
                'profile_photo_url' => $userData['profile_photo_url'],
                'latitude' => $userData['latitude'],
                'longitude' => $userData['longitude'],
            ]);
            DB::commit();
            return $updated;
        } 
        catch (Exception $e) 
        {
            DB::rollBack();
            throw $e;
        }
    }

    public function storeStudentRole(User $user, Collection $studentData)
    {
        DB::beginTransaction();
        try {
            $student = Student::updateOrCreate(
                ['user_id' => $user->id],
                [
                    'parent' => $studentData['parent'],
                    'parent_telephone_number' => $studentData['parent_telephone_number'],
                    'class_id' => $studentData['class_id'],
                    'curriculum_id' => $studentData['curriculum_id'],
                ]
            );
            DB::commit();
            return $student;
        } catch(Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function storeTutorRole(User $user, Collection $tutorData) {
        // Log::info($tutorData['experience']);
        DB::beginTransaction();
        try {
            $tutor = Tutor::updateOrCreate(
                ['user_id' => $user->id],
                [
                    'experience' => $tutorData['experience'],
                    'organization' => $tutorData['organization'],
                    'course_mode' => $tutorData['course_mode'],
                    'description' => $tutorData['description'],
                    'qualification' => $tutorData['qualification'],
                    'learning_method' => $tutorData['learning_method'],
                    'status' => TutorStatusEnum::VERIFY->value,
                ],
            );

            DB::commit();
            return $tutor;
        } catch(Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
}
