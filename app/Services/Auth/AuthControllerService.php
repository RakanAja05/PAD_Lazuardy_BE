<?php

namespace App\Services\Auth;

use App\DTOs\ResponseDTO;
use App\Enums\BadgeEnum;
use App\Enums\GenderEnum;
use App\Enums\OtpIdentifierEnum;
use App\Enums\OtpTypeEnum;
use App\Enums\RoleEnum;
use App\Http\Requests\StoreStudentRegisterRequest;
use App\Http\Requests\StoreTutorRegisterRequest;
use App\Http\Requests\UpdateAuthRequest;
use App\Http\Requests\VerifyOtpRequest;
use App\Models\ClassModel;
use App\Models\Curriculum;
use App\Models\Student;
use App\Models\Tutor;
use App\Models\User;
use App\Services\AuthService;
use App\Services\OtpService;
use App\Services\UserService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class AuthControllerService
{
    public function sendRegisterOtp(Request $request): ResponseDTO
    {
        $data = $request->validate([
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $otpService = new OtpService();
        $otp = $otpService->createOtp($data['email'], OtpIdentifierEnum::EMAIL->value, OtpTypeEnum::REGISTER->value);

        return new ResponseDTO([
            'status' => 'success',
            'message' => 'OTP berhasil terkirim ke email',
            'data' => [
                'otp' => $otp['code'],
            ],
        ], 201);
    }

    public function verifyRegisterOtp(Request $request): ResponseDTO
    {
        $request->validate([
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'otp' => ['required', 'string'],
        ]);

        $otpService = new OtpService();
        $result = $otpService->checkOtp(
            $request['otp'],
            $request['email'],
            OtpIdentifierEnum::EMAIL->value,
            OtpTypeEnum::REGISTER->value
        );

        $payload = [
            'status' => $result['status'],
            'message' => $result['message'],
        ];

        if ($result['status'] === 'success') {
            $payload['data'] = [
                'identifier' => $result['identifier'] ?? null,
            ];
        } else {
            $payload['errors'] = [
                'identifier' => $result['identifier'] ?? null,
                'code' => $result['code'] ?? null,
            ];
        }

        return new ResponseDTO($payload, $result['code']);
    }

    public function resendRegisterOtp(Request $request): ResponseDTO
    {
        $request->validate([
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
        ]);

        $otpService = new OtpService();
        $result = $otpService->resendOtp(
            $request['email'],
            OtpIdentifierEnum::EMAIL->value,
            OtpTypeEnum::REGISTER->value
        );

        $payload = [
            'status' => $result['status'],
            'message' => $result['message'],
        ];

        if ($result['status'] === 'success') {
            $payload['data'] = [
                'otp' => $result['otp_code'] ?? null,
            ];
        } else {
            $payload['errors'] = [
                'detail' => $result['message'],
            ];
        }

        return new ResponseDTO($payload, $result['code']);
    }

    public function storeStudentRegister(StoreStudentRegisterRequest $request): ResponseDTO
    {
        $request->validated();

        $userService = new UserService();
        $authService = new AuthService();

        $userData = $request->only(
            'email', 'password', 'name',
            'gender', 'date_of_birth',
            'telephone_number', 'religion',
            'latitude', 'longitude'
        );

        if ($request->hasFile('profile_photo')) {
            $file = $request->file('profile_photo');
            $path = $file->store('uploads', 'public');
            $userData['profile_photo_url'] = $path;
        }

        $userData['gender'] = GenderEnum::tryFromDisplayName($request->gender);
        $userData['password'] = Hash::make($userData['password']);
        $userData['role'] = RoleEnum::STUDENT;
        $userData['home_address'] = $userService->convertAddressToArray(
            $request->only(['province', 'regency', 'district', 'subdistrict', 'street'])
        );

        $studentData = $request->only([
            'class_id', 'curriculum_id',
            'school', 'parent',
            'parent_telephone_number',
        ]);
        $studentData = $this->resolveStudentRefs($studentData);

        DB::beginTransaction();
        try {
            $userResult = $authService->registerUser($userData);
            $studentData['user_id'] = $userResult['user']->id;
            Student::create($studentData);

            DB::commit();

            return new ResponseDTO([
                'status' => 'success',
                'message' => 'Registrasi akun berhasil',
                'data' => [
                    'token' => $userResult['token'],
                ],
            ], 201);
        } catch (Exception $e) {
            DB::rollBack();

            return new ResponseDTO([
                'status' => 'error',
                'message' => 'Registrasi gagal: ' . $e->getMessage(),
                'errors' => [
                    'code' => $e->getCode(),
                ],
            ], 500);
        }
    }

    public function storeTutorRegister(StoreTutorRegisterRequest $request): ResponseDTO
    {
        $request->validated();

        $userService = new UserService();
        $authService = new AuthService();

        $userData = $request->only(
            'email', 'password', 'name',
            'gender', 'date_of_birth',
            'telephone_number', 'religion',
            'latitude', 'longitude'
        );

        if ($request->hasFile('profile_photo')) {
            $file = $request->file('profile_photo');
            $path = $file->store('uploads', 'public');
            $userData['profile_photo_url'] = $path;
        }

        $userData['password'] = Hash::make($userData['password']);
        $userData['role'] = RoleEnum::TUTOR;
        $userData['home_address'] = $userService->convertAddressToArray(
            $request->only(['province', 'regency', 'district', 'subdistrict', 'street'])
        );

        $tutorData = $request->only(['bank', 'rekening']);
        $tutorData['badge'] = BadgeEnum::BRONZE;

        DB::beginTransaction();
        try {
            $userResult = $authService->registerUser($userData);
            $tutorData['user_id'] = $userResult['user']->id;
            Tutor::create($tutorData);

            DB::commit();

            return new ResponseDTO([
                'status' => 'success',
                'message' => 'Registrasi akun berhasil',
                'data' => [
                    'token' => $userResult['token'],
                ],
            ], 201);
        } catch (Exception $e) {
            DB::rollBack();

            return new ResponseDTO([
                'status' => 'error',
                'message' => 'Registrasi gagal: ' . $e->getMessage(),
                'errors' => [
                    'code' => $e->getCode(),
                ],
            ], 500);
        }
    }

    public function forgotPassword(Request $request): ResponseDTO
    {
        $validatedData = $request->validate([
            'email' => ['required', 'email', 'exists:users,email'],
        ]);

        $otpService = new OtpService();
        $user = User::getUserByEmail($validatedData['email']);

        if (!$user->exists()) {
            return new ResponseDTO([
                'status' => 'error',
                'message' => 'Email tidak ditemukan.',
                'errors' => [
                    'email' => $validatedData['email'],
                ],
            ], 404);
        }

        DB::beginTransaction();
        try {
            $otp = $otpService->createOtp(
                $user->email,
                OtpIdentifierEnum::EMAIL->value,
                OtpTypeEnum::FORGOT_PASSWORD->value,
                10,
                $user->id
            );
            $data = [
                'identifier' => $user->email,
                'identifier_type' => OtpIdentifierEnum::EMAIL->value,
                'verification_type' => OtpTypeEnum::FORGOT_PASSWORD->value,
            ];
            $caching = $otpService->storeOtpToCache(OtpTypeEnum::FORGOT_PASSWORD->value, $data);
            DB::commit();

            return new ResponseDTO([
                'status' => 'success',
                'message' => 'OTP untuk reset password telah dikirim ke email Anda.',
                'data' => [
                    'otp' => $otp['code'],
                    'temp_token' => $caching['token'],
                ],
            ], 200);
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function verifyForgotPassword(VerifyOtpRequest $request): ResponseDTO
    {
        $request->validated();
        $otpService = new OtpService();
        $verify = $otpService->verifyOtp(
            $request->otp_code,
            OtpTypeEnum::FORGOT_PASSWORD->value,
            $request->temp_token
        );

        $tokenReset = Str::random(15);
        Cache::put('auth:reset-password:' . $tokenReset, ['email' => $verify['identifier']], 1800);

        $payload = [
            'status' => $verify['status'],
            'message' => $verify['message'],
        ];

        if ($verify['status'] === 'success') {
            $payload['data'] = [
                'token' => $tokenReset,
            ];
        } else {
            $payload['errors'] = [
                'identifier' => $verify['identifier'] ?? null,
                'code' => $verify['code'] ?? null,
            ];
        }

        return new ResponseDTO($payload, $verify['code']);
    }

    public function resetPassword(UpdateAuthRequest $request): ResponseDTO
    {
        $validatedData = $request->validated();

        $cacheKey = 'auth:reset-password:' . $validatedData['token'];
        $cacheData = Cache::get($cacheKey);
        $user = User::getUserByEmail($cacheData['email']);

        if (!$user->exists()) {
            return new ResponseDTO([
                'status' => 'error',
                'message' => 'Email tidak ditemukan.',
                'errors' => [
                    'email' => $cacheData['email'] ?? null,
                ],
            ], 404);
        }

        DB::beginTransaction();
        try {
            $user->password = Hash::make($validatedData['password']);
            $user->save();
            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }

        return new ResponseDTO([
            'status' => 'success',
            'message' => 'Password berhasil direset.',
            'data' => [],
        ], 200);
    }

    private function resolveStudentRefs(array $studentData): array
    {
        if (!empty($studentData['class_id']) && !ClassModel::whereKey($studentData['class_id'])->exists()) {
            $classId = ClassModel::whereRaw('LOWER(name) = ?', [strtolower((string) $studentData['class_id'])])
                ->value('id');
            if ($classId) {
                $studentData['class_id'] = $classId;
            }
        }

        if (!empty($studentData['curriculum_id']) && !Curriculum::whereKey($studentData['curriculum_id'])->exists()) {
            $curriculumId = Curriculum::whereRaw('LOWER(name) = ?', [strtolower((string) $studentData['curriculum_id'])])
                ->value('id');
            if ($curriculumId) {
                $studentData['curriculum_id'] = $curriculumId;
            }
        }

        return $studentData;
    }
}
