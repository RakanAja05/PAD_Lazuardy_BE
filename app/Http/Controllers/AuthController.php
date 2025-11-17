<?php

namespace App\Http\Controllers;

use App\Enums\BadgeEnum;
use App\Enums\OtpIdentifierEnum;
use App\Enums\OtpTypeEnum;
use App\Enums\RoleEnum;
use App\Http\Requests\StoreStudentRegisterRequest;
use App\Http\Requests\StoreTutorRegisterRequest;
use App\Http\Requests\UpdateAuthRequest;
use App\Http\Requests\VerifyOtpRequest;
use App\Models\Student;
use App\Models\Tutor;
use App\Models\User;
use App\Services\AuthService;
use App\Services\OtpService;
use App\Services\UserService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Cache;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|string|email',
            'password' => 'required|string',
        ]);

        $user = User::where('email', $request->email)->first();

        if (! $user || ! Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['Email atau password salah.'],
            ]);
        }

        // hapus token lama biar tidak dobel
        $user->tokens()->delete();

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message' => 'Login berhasil',
            'token' => $token,
            'role' => $user->role,
        ]);
    }
    

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Logout berhasil',
        ]);
    }

    public function me(Request $request)
    {
        return response()->json($request->user());
    }

    public function sendRegisterOtp(Request $request)
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

    public function verifyRegisterOtp(Request $request)
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

    public function resendRegisterOtp(Request $request)
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
            $studentData['user_id'] = $userResult['user']->id;
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

    
    public function forgotPassword(Request $request)
    {
        $validatedData = $request->validate([
            'email' => ['required', 'email', 'exists:users,email'],
        ]);

        $otpService = new OtpService;

        $user = User::getUserByEmail($validatedData['email']);
        
        if (!$user->exists()) {
            return response()->json([
                "message" => "Email tidak ditemukan."
            ], 404);
        }

        DB::beginTransaction();
        try{
            $otp = $otpService->createOtp($user->email, OtpIdentifierEnum::EMAIL->value, OtpTypeEnum::FORGOT_PASSWORD->value, 10, $user->id);
            $data = [
                'identifier' => $user->email,
                'identifier_type' => OtpIdentifierEnum::EMAIL->value,
                'verification_type' => OtpTypeEnum::FORGOT_PASSWORD->value,
            ];
            $caching = $otpService->storeOtpToCache(OtpTypeEnum::FORGOT_PASSWORD->value, $data);
            DB::commit();
            return response()->json([
                "message" => "OTP untuk reset password telah dikirim ke email Anda.",
                "otp" => $otp['code'],
                "temp_token" => $caching['token']
            ], 200);
        } catch (Exception $e){
            DB::rollBack();
            throw $e;
        }
        
    }

    public function verifyForgotPassword(VerifyOtpRequest $request)
    {
        $request->validated();
        $otpService = new OtpService;
        $verify = $otpService->verifyOtp($request->otp_code, OtpTypeEnum::FORGOT_PASSWORD->value, $request->temp_token);
        $tokenReset = Str::random(15);
        Cache::put('auth:reset-password:' . $tokenReset, ["email" => $verify['identifier']], 1800);

        return response()->json([
            "status" => $verify['status'],
            "message" => $verify['message'],
            "token" => $tokenReset
        ], $verify["code"]);
    }

    
    public function resetPassword(UpdateAuthRequest $request)
    {
        $validatedData = $request->validated();

        $cache_key = 'auth:reset-password:' . $validatedData['token'];
        $cache_data = Cache::get($cache_key);
        $user = User::getUserByEmail($cache_data['email']);
        

        if (!$user->exists()) {
            return response()->json([
                "message" => "Email tidak ditemukan."
            ], 404);
        }

        DB::beginTransaction();
        try{
            $user->password = Hash::make($validatedData['password']);
            $user->save();
            DB::commit();
        } catch (Exception $e){
            DB::rollBack();
            throw $e;
        }

        return response()->json([
            "message" => "Password berhasil direset."
        ], 200);
    }
}
