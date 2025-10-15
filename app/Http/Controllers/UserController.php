<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreStudentRegisterRequest;
use App\Http\Requests\StoreTutorRegisterRequest;
use App\Models\User;
use App\Services\ScheduleService;
use App\Services\TutorService;
use App\Services\UserService;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;

class UserController extends Controller
{
    /**
     * Update the specified resource in storage.
     * @OA\Patch(
     * path="api/register/student/{user}",
     * operationId="regStudentRole",
     * tags={"Users", "Students"},
     * @OA\Parameter(
     * name="user",
     * in="path",
     * required=true,
     * description="user adalah ID user yang melakukan registrasi",
     * @OA\Schema(type="integer"),
     * ),
     * @OA\Response(
     * response=200,
     * description="Data berhasil diubah"
     * )
     * )
     */
    public function updateStudentRole(StoreStudentRegisterRequest $request, UserService $userService)
    {
        $user = $request->user();
        
        $validatedData = $request->validated();
        
        $userData = collect(Arr::only($validatedData, ['name', 'gender', 'date_of_birth', 'telephone_number', 'profile_photo_url', 'latitude', 'longitude']));
        $addressData = collect(Arr::only($validatedData, ['province', 'regency', 'district', 'subdistrict', 'street']));
        $studentData = collect(Arr::only($validatedData, ['parent', 'parent_telephone_number', 'class_id']));

        $userService->updateBiodataRegistration($user, $userData, $addressData);
        $userService->storeStudentRole($user, $studentData);

        return response()->json([
            'message' => 'Berhasil'
        ], 200);;
    }
    
    /**
     * Update registrasi role tutor
     * @OA\Patch(
     * path="api/register/tutor/{user}",
     * operationId="regTutorRole",
     * tags={"Users", "Tutors"},
     * @OA\Parameter(
     * name="user",
     * in="path",
     * required=true,
     * description="user adalah ID user yang melakukan registrasi",
     * @OA\Schema(type="integer"),
     * ),
     * @OA\Response(
     * response=200,
     * description="Data berhasil diubah"
     * )
     * )
     */
    public function updateTutorRole(StoreTutorRegisterRequest $request, UserService $userService, TutorService $tutorService, ScheduleService $scheduleService)
    {
        $user = $request->user();

        $validatedData = $request->validated();

        $userData = collect(Arr::only($validatedData, ['name', 'gender', 'date_of_birth', 'telephone_number', 'profile_photo_url', 'latitude', 'longitude']));
        $addressData = collect(Arr::only($validatedData, ['province', 'regency', 'district', 'subdistrict', 'street']));
        $tutorData = collect(Arr::only($validatedData, ['experience', 'organization','course_location', 'description', 'qualification', 'learning_method'],));
        $subjectData = collect(Arr::only($validatedData, ['subject_ids']));
        $fileData = collect(Arr::only($validatedData, ['cv', 'ktp','ijazah','certificate', 'portofolio']));
        $scheduleDatas = collect(Arr::only($validatedData, ['schedules']));
        
        $userService->updateBiodataRegistration($user, $userData, $addressData);
        $userService->storeTutorRole($user, $tutorData);
        $tutorService->storeSubject($user, $subjectData);
        $tutorService->storeFile($user, $fileData);
        $scheduleService->storeScheduleTutor($user, $scheduleDatas);

        
        return response()->json([
            'message' => 'Berhasil'
        ], 200);;
    }
}
