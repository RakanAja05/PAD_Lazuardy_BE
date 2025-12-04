<?php

namespace App\Http\Controllers;

use App\Enums\BadgeEnum;
use App\Enums\GenderEnum;
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
    /**
     * @OA\Post(
     *     path="/api/register",
     *     tags={"Authentication"},
     *     summary="Send OTP for registration",
     *     description="Langkah 1 registrasi: Kirim email & password, sistem akan mengirim OTP ke email.",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"email", "password", "password_confirmation"},
     *             @OA\Property(property="email", type="string", format="email", example="newuser@example.com"),
     *             @OA\Property(property="password", type="string", format="password", example="password123", description="Min 8 karakter"),
     *             @OA\Property(property="password_confirmation", type="string", format="password", example="password123")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="OTP sent",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string", example="OTP berhasil terkirim ke email"),
     *             @OA\Property(property="otp", type="string", example="123456", description="OTP code (hanya untuk development)")
     *         )
     *     ),
     *     @OA\Response(response=422, description="Validation error - Email sudah terdaftar")
     * )
     */
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

    /**
     * @OA\Patch(
     *     path="/api/register/verify",
     *     tags={"Authentication"},
     *     summary="Verify OTP for registration",
     *     description="Langkah 2 registrasi: Verifikasi OTP yang dikirim ke email.",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"email", "otp"},
     *             @OA\Property(property="email", type="string", format="email", example="newuser@example.com"),
     *             @OA\Property(property="otp", type="string", example="123456")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="OTP verified",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string", example="OTP berhasil diverifikasi")
     *         )
     *     ),
     *     @OA\Response(response=400, description="OTP invalid atau expired"),
     *     @OA\Response(response=422, description="Validation error")
     * )
     */
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

    /**
     * @OA\Patch(
     *     path="/api/register/student",
     *     tags={"Authentication"},
     *     summary="Complete student registration",
     *     description="Langkah 3 registrasi student: Lengkapi data profil student setelah OTP terverifikasi.",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 required={"email", "password", "name", "gender", "date_of_birth", "telephone_number", "class_id", "curriculum_id"},
     *                 @OA\Property(property="email", type="string", format="email"),
     *                 @OA\Property(property="password", type="string", format="password"),
     *                 @OA\Property(property="name", type="string", example="Andi Wijaya"),
     *                 @OA\Property(property="gender", type="string", enum={"pria", "wanita"}),
     *                 @OA\Property(property="date_of_birth", type="string", format="date", example="2005-01-15"),
     *                 @OA\Property(property="telephone_number", type="string", example="081234567890"),
     *                 @OA\Property(property="religion", type="string", enum={"islam", "kristen", "katolik", "hindu", "buddha", "konghucu"}),
     *                 @OA\Property(property="class_id", type="integer", example=1),
     *                 @OA\Property(property="curriculum_id", type="integer", example=1),
     *                 @OA\Property(property="school", type="string", example="SMA Negeri 1"),
     *                 @OA\Property(property="parent", type="string", example="Budi Santoso"),
     *                 @OA\Property(property="parent_telephone_number", type="string", example="081234567891"),
     *                 @OA\Property(property="province", type="string", example="Jawa Timur"),
     *                 @OA\Property(property="regency", type="string", example="Surabaya"),
     *                 @OA\Property(property="district", type="string", example="Rungkut"),
     *                 @OA\Property(property="subdistrict", type="string", example="Rungkut Kidul"),
     *                 @OA\Property(property="street", type="string", example="Jl. Raya Kalirungkut No. 1"),
     *                 @OA\Property(property="latitude", type="number", format="double", example=-7.3297),
     *                 @OA\Property(property="longitude", type="number", format="double", example=112.7814),
     *                 @OA\Property(property="profile_photo", type="string", format="binary", description="Optional profile photo")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Registration successful",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="token", type="string", example="1|abc123..."),
     *             @OA\Property(property="message", type="string", example="Registrasi akun berhasil")
     *         )
     *     ),
     *     @OA\Response(response=422, description="Validation error"),
     *     @OA\Response(response=500, description="Registration failed")
     * )
     */
    public function storeStudentRegister(StoreStudentRegisterRequest $request)
    {
        $request->validated();

        $userService = new UserService;
        $authService = new AuthService;

        $userData = $request->only(
            'email', 'password','name', 
            'gender', 'date_of_birth',
            'telephone_number', 'religion',
            'latitude', 'longitude',
        );
        
        if ($request->hasFile('profile_photo')){
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
            'telephone_number', 'religion',
            'latitude', 'longitude',
        );

        if ($request->hasFile('profile_photo')){
            $file = $request->file('profile_photo');
            $path = $file->store('uploads', 'public');

            $userData['profile_photo_url'] = $path;
        }

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
            $tutorData['user_id'] = $userResult["user"]->id;
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
