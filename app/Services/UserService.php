<?php

namespace App\Services;

use App\Enums\TutorStatus;
use App\Models\Student;
use App\Models\Tutor;
use App\Models\User;
use Exception;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class UserService
{
    public function updateBiodataRegistration(User $user, Collection $userData, Collection $addressData){
        DB::beginTransaction();
        try 
        {
            $updated = $user->update([
                'name' => $userData['name'],
                'role' => 'student',
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
                    'course_location' => $tutorData['course_location'],
                    'description' => $tutorData['description'],
                    'qualification' => $tutorData['qualification'],
                    'learning_method' => $tutorData['learning_method'],
                    'status' => TutorStatus::VERIFY->value,
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
