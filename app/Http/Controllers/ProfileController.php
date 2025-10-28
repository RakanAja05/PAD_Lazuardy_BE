<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateStudentProfileRequest;
use App\Services\StudentService;
use App\Services\TutorService;
use App\Services\UserService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProfileController extends Controller
{
    public function showStudentProfile(Request $request)
    {
        $user = $request->user()->load(['student.class']);
        $student = $user->student;

        $userService = new UserService;
        $studentService = new StudentService;

        $userData = $userService->showUserProfile($user);
        $studentData = $studentService->showStudentProfile($student);
        $message = ['message' => 'success'];
        
        $data = array_merge($userData, $studentData, $message);

        return response()->json($data, 200);
    }

    public function updateStudentProfile(UpdateStudentProfileRequest $request)
    {
        $request->validated();

        $user = $request->user()->load(['student']);
        $student = $user->student;

        $userService = new UserService;

        $address = $userService->convertAddressToJson($request);
        $userData = $request->only(['name', 'telephone_number', 'profile_photo_url', 'gender', 'date_of_birth', 'religion']);
        $userData['home_address'] = $address;
        
        DB::beginTransaction();
        try 
        {
            $user->update($userData);
            $student->update($request->only(['school', 'class_id', 'curriculum_id', 'parent', 'parent_telephone_number']));
            DB::commit();
            return response()->json([
                'status' => 'success',
                'message' => 'Update data berhasil'
            ],200);
        } 
        catch(Exception $e) 
        {
            DB::rollBack();
            
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal mengupdate profil: ' . $e->getMessage(),
                // 'error_detail' => $e->getMessage(), //hanya untuk debugging
                'error_code' => $e->getCode(),
            ], 500); 
        }
    }
}
