<?php

namespace App\Http\Controllers;

use App\Enums\BadgeEnum;
use App\Enums\OtpIdentifierEnum;
use App\Enums\OtpTypeEnum;
use App\Enums\RoleEnum;
use App\Http\Requests\StoreStudentRegisterRequest;
use App\Http\Requests\StoreTutorRegisterRequest;
use App\Models\Student;
use App\Models\Tutor;
use App\Services\AuthService;
use App\Services\OtpService;
use App\Services\UserService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class AuthController2 extends Controller
{
    // Register
    public function sendOtpRegister(Request $request)
    {
        // validasi
        $data = $request->validate([
            'email' => ['required','string','email','max:255','unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        // panggil objek service otp
        $otpService = new OtpService;
        

        // otp dibuat diservice
        $otp = $otpService->createOtp($data['email'], OtpIdentifierEnum::EMAIL->value, OtpTypeEnum::REGISTER->value);

        return response()->json([
            'status' => 'success',
            'message' => 'OTP berhasil terkirim ke email',
            'otp' => $otp['code']
        ], 201);
    }

    public function verifyOtpRegister(Request $request)
    {
        // validasi
        $request->validate([
            'email' => ['required','string','email','max:255','unique:users'],
            "otp" => ["required", "string"],
        ]);

        $otpService = new OtpService;
        $result = $otpService->checkOtp(
            $request['otp'],
            $request['email'],
            OtpIdentifierEnum::EMAIL->value,
            OtpTypeEnum::REGISTER->value
        );

        return response()->json([
            'status' => $result['status'],
            'message' => $result['message'],
        ], $result['code']);
    }

    public function resendOtpRegister(Request $request)
    {
        $request->validate([
            'email' => ['required','string','email','max:255','unique:users']
        ]);

        $otpService = new OtpService;

        $result = $otpService->resendOtp(
            $request['email'],
            OtpIdentifierEnum::EMAIL->value,
            OtpTypeEnum::REGISTER->value
        );

        return response()->json([
            'status' => $result['status'],
            'message' => $result['message'],
            'otp' => $result['otp_code'] ?? null,
        ], $result['code']);
    }

    public function storeStudentRegister(StoreStudentRegisterRequest $request)
    {
        $request->validated();

        $userService = new UserService;
        $authService = new AuthService;

        $userData = $request->only(
            'email', 'password','name', 
            'gender', 'date_of_birth',
            'telephone_number', 'profile_photo_url', 
            'latitude', 'longitude',
        );
        $userData['password'] = Hash::make($userData['password']);
        $userData['role'] = RoleEnum::STUDENT;
        $userData['home_address'] = $userService->convertAddressToArray(
            $request->only(['province', 'regency', 'district', 'subdistrict', 'street'])
        );

        $studentData = $request->only([
            'class_id', 'curriculum_id', 
            'school','parent', 
            'parent_telephone_number', 
        ]);

        DB::beginTransaction();
        try 
        {
            // Query untuk masukin ke database masuk ke service
            $userResult = $authService->registerUser($userData);
            Student::create($studentData);

            DB::commit();
            return response()->json([
                "status" => "success",
                "token" => $userResult['token'],
                "message" => "Registrasi akun berhasil",
            ], 201);
        } catch (Exception $e)
        {
            DB::rollBack();
            return response()->json([
                "status" => "error",
                "message" => "Registrasi gagal: " . $e->getMessage(),
                "error_code" => $e->getCode(),
            ], 500);
        }
    }

    public function storeTutorRegister(StoreTutorRegisterRequest $request)
    {
        $request->validated();

        $userService = new UserService;
        $authService = new AuthService;

        // filter data user
        $userData = $request->only(
            'email', 'password','name', 
            'gender', 'date_of_birth',
            'telephone_number', 'profile_photo_url', 
            'latitude', 'longitude',
        );
        $userData['password'] = Hash::make($userData['password']);
        $userData['role'] = RoleEnum::TUTOR;
        $userData['home_address'] = $userService->convertAddressToArray(
            $request->only(['province', 'regency', 'district', 'subdistrict', 'street'])
        );

        // filter data tutor
        $tutorData = $request->only(['bank', 'rekening',]);
        $tutorData['badge'] = BadgeEnum::BRONZE;

        DB::beginTransaction();
        try 
        {
            $userResult = $authService->registerUser($userData);
            Tutor::create($tutorData);
            
            DB::commit();
            return response()->json([
                "status" => "success",
                "token" => $userResult['token'],
                "message" => "Registrasi akun berhasil",
            ], 201);
        } catch (Exception $e)
        {
            DB::rollBack();
            return response()->json([
                "status" => "error",
                "message" => "Registrasi gagal: " . $e->getMessage(),
                "error_code" => $e->getCode(),
            ], 500);
        }
    }
}
