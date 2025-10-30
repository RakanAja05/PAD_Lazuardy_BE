<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateStudentProfileRequest;
use App\Http\Requests\UpdateTutorProfileRequest;
use App\Models\Tutor;
use App\Services\OpenCageService;
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
        $message = ['status' => 'success'];
        
        $data = array_merge($userData, $studentData, $message);

        return response()->json($data, 200);
    }

    public function showTutorProfile(Request $request)
    {
        $user = $request->user()->load(['tutor']);
        $tutor = $user->tutor;

        $userService = new UserService;

        $userData = $userService->showUserProfile($user);
        $message = ['message' => 'success'];
        
        $data = array_merge($userData, $message);

        return response()->json($data, 200);
    }

    public function updateStudentProfile(UpdateStudentProfileRequest $request)
    {
        $request->validated();

        $user = $request->user()->load(['student']);
        $student = $user->student;

        $userService = new UserService;
        $openCageService = new OpenCageService;

        $address = $userService->convertAddressToArray($request);
        $string_address = $userService->convertAddressToString($address);
        $geocode = $openCageService->fordwardGeocode($string_address['fullAddress'], $string_address["simplifiedAddress"]);
        $coordinate['latitude'] = $geocode['latitude'];
        $coordinate['longitude'] = $geocode['longitude'];
        $userData = $request->only(['name', 'telephone_number', 'profile_photo_url', 'gender', 'date_of_birth', 'religion']);
        $userData['home_address'] = $address;
        $data = array_merge($userData, $coordinate);
        
        DB::beginTransaction();
        try 
        {
            $user->update($data);
            $student->update($request->only(['school', 'class_id', 'curriculum_id', 'parent', 'parent_telephone_number']));
            DB::commit();
            return response()->json([
                'status' => 'success',
                'message' => 'Profile berhasil di update',
                "alamat" => $userService->convertAddressToString($address),
                'tes' => $geocode
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
    
    public function updateTutorProfile(UpdateTutorProfileRequest $request)
    {
        $request->validated();

        $user = $request->user()->load(['tutor']);
        $tutor = $user->tutor;

        $userService = new UserService;
        $openCageService = new OpenCageService;

        $address = $userService->convertAddressToArray($request);
        $string_address = $userService->convertAddressToString($address);
        $geocode = $openCageService->fordwardGeocode($string_address['fullAddress'], $string_address["simplifiedAddress"]);
        $coordinate['latitude'] = $geocode['latitude'];
        $coordinate['longitude'] = $geocode['longitude'];
        $userData = $request->only(['name', 'telephone_number', 'profile_photo_url', 'gender', 'date_of_birth', 'religion']);
        $userData['home_address'] = $address;
        $data = array_merge($userData, $coordinate);

        try{

            $user->update($data);

            return response()->json([
                'status' => 'success',
                'message' => 'Profile berhasil di update',
                "alamat" => $userService->convertAddressToString($address),
                'tes' => $geocode
            ], 200);
        } catch(Exception $e) {

            return response()->json([
                'status' => 'error',
                'message' => 'Gagal mengupdate profile' . $e->getMessage(),
                'error_code' => $e->getCode()
            ], 500);
        }
    }
}
