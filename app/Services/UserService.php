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
    public function convertAddress($data)
    {
        return 
            [
            "province" => $data["province"],
            "regency" => $data["regency"],
            "district" => $data["district"],
            "subdistrict" => $data["subdistrict"],
            "street" => $data["street"],
            ];
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

    public function updateBiodataRegistration(User $query, $data){
        return $query->update([
                'name' => $data['name'],
                'role' => RoleEnum::STUDENT->value,
                'gender' => $data['gender'],
                'date_of_birth' => $data['date_of_birth'],
                'telephone_number' => $data['telephone_number'],
                'home_address' => $data['home_address'],
                'profile_photo_url' => $data['profile_photo_url'],
                'latitude' => $data['latitude'],
                'longitude' => $data['longitude'],
            ]);
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
