<?php

namespace App\Services\Students;

use App\DTOs\ResponseDTO;
use App\Http\Requests\UpdateStudentProfileRequest;
use App\Http\Requests\UpdateTutorLessonMethodRequest;
use App\Http\Requests\UpdateTutorProfileRequest;
use App\Services\StudentService;
use App\Services\UserService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProfileService
{
    public function showStudentProfile(Request $request): ResponseDTO
    {
        $user = $request->user()->load(['student.class']);
        $student = $user->student;

        $userService = new UserService();
        $studentService = new StudentService();

        $userData = $userService->showUserProfile($user);
        $studentData = $studentService->showStudentProfile($student);

        $data = array_merge($userData, $studentData);

        return new ResponseDTO([
            'status' => 'success',
            'message' => 'Berhasil mengambil profil student',
            'data' => $data,
        ], 200);
    }

    public function showTutorProfile(Request $request): ResponseDTO
    {
        $user = $request->user()->load(['tutor']);

        $userService = new UserService();
        $userData = $userService->showUserProfile($user);

        $data = $userData;

        return new ResponseDTO([
            'status' => 'success',
            'message' => 'Berhasil mengambil profil tutor',
            'data' => $data,
        ], 200);
    }

    public function updateStudentProfile(UpdateStudentProfileRequest $request): ResponseDTO
    {
        $request->validated();

        $user = $request->user()->load(['student']);
        $student = $user->student;

        $userService = new UserService();

        $address = $userService->convertAddressToArray($request);
        $userData = $request->only([
            'name',
            'telephone_number',
            'profile_photo_url',
            'gender',
            'date_of_birth',
            'religion',
            'latitude',
            'longitude',
        ]);
        $userData['home_address'] = $address;

        DB::beginTransaction();
        try {
            $user->update($userData);
            $student->update($request->only([
                'school',
                'class_id',
                'curriculum_id',
                'parent',
                'parent_telephone_number',
            ]));
            DB::commit();

            return new ResponseDTO([
                'status' => 'success',
                'message' => 'Profile berhasil di update',
                'data' => [],
            ], 200);
        } catch (Exception $e) {
            DB::rollBack();

            return new ResponseDTO([
                'status' => 'error',
                'message' => 'Gagal mengupdate profil: ' . $e->getMessage(),
                'errors' => [
                    'code' => $e->getCode(),
                ],
            ], 500);
        }
    }

    public function updateTutorProfile(UpdateTutorProfileRequest $request): ResponseDTO
    {
        $request->validated();

        $user = $request->user()->load(['tutor']);
        $tutor = $user->tutor;
        $userData = $request->only([
            'name',
            'gender',
            'date_of_birth',
            'religion',
            'telephone_number',
            'latitude',
            'longitude',
        ]);

        $rawAddress = $request->only([
            'province',
            'regency',
            'district',
            'subdistrict',
            'street',
        ]);

        $tutorData = $request->only([
            'bank',
            'rekening',
        ]);

        $userService = new UserService();
        $address = $userService->convertAddressToArray($rawAddress);
        $userData = array_merge($userData, $address);

        DB::beginTransaction();
        try {
            $user->update($userData);
            $tutor->update($tutorData);

            DB::commit();

            return new ResponseDTO([
                'status' => 'success',
                'message' => 'Profile berhasil di update',
                'data' => [],
            ], 200);
        } catch (Exception $e) {
            DB::rollBack();

            return new ResponseDTO([
                'status' => 'error',
                'message' => 'Gagal mengupdate profile' . $e->getMessage(),
                'errors' => [
                    'code' => $e->getCode(),
                ],
            ], 500);
        }
    }

    public function showTutorLessonMethod(Request $request): ResponseDTO
    {
        $user = $request->user()->load(['tutor', 'schedules']);
        $tutor = $user->tutor;

        $data = [
            'course_mode' => $tutor->course_mode,
            'description' => $tutor->description,
            'qualification' => $tutor->qualification,
            'learning_method' => $tutor->learning_method,
            'schedules' => $user->schedules,
        ];

        return new ResponseDTO([
            'status' => 'success',
            'message' => 'Berhasil mengambil metode belajar tutor',
            'data' => $data,
        ], 200);
    }

    public function updateTutorLessonMethod(UpdateTutorLessonMethodRequest $request): ResponseDTO
    {
        $user = $request->user()->load(['tutor', 'schedules']);
        $tutor = $user->tutor;

        $tutorData = $request->only([
            'course_mode',
            'description',
            'qualification',
            'learning_method',
        ]);

        $schedulesData = $request->input('schedules');

        DB::beginTransaction();
        try {
            $tutor->update($tutorData);
            $user->schedules()->delete();

            $schedulesToCreate = collect($schedulesData)->map(function ($schedule) use ($user) {
                return array_merge($schedule, ['user_id' => $user->id]);
            })->toArray();

            $user->schedules()->insert($schedulesToCreate);
            DB::commit();

            return new ResponseDTO([
                'status' => 'success',
                'message' => 'Profile dan jadwal tutor berhasil diperbarui.',
                'data' => [],
            ], 200);
        } catch (Exception $e) {
            DB::rollBack();

            return new ResponseDTO([
                'status' => 'error',
                'message' => 'Profile dan jadwal tutor gagal diperbarui: ' . $e->getMessage(),
                'errors' => [
                    'detail' => $e->getMessage(),
                ],
            ], 500);
        }
    }
}
