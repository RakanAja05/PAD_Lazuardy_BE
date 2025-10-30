<?php

namespace App\Http\Controllers;

use App\Enums\OtpTypeEnum;
use App\Enums\VerificationTypeEnum;
use App\Http\Requests\StoreOtpRequest;
use App\Http\Requests\InitiateRegiterRequest;
use App\Http\Requests\UpdateAuthRequest;
use App\Http\Requests\UpdateOtpRequest;
use App\Http\Requests\VerifyOtpRequest;
use App\Models\User;
use App\Services\AuthService;
use App\Services\OtpService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str; 
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    /**
     * Initiate user registration process
     * @OA\Post(
     * path="api/register",
     * operationId="initiateRegister",
     * summary="User menginput data email dan password untuk registrasi",
     * description="Endpoint untuk memulai proses registrasi user baru dengan mengirimkan email dan password. Sistem akan mengirimkan OTP ke email yang didaftarkan untuk verifikasi.",
     * tags={"Authentication"},
     * @OA\RequestBody(
     *    required=true,
     *    description="Data registrasi user",
     *    @OA\JsonContent(
     *       required={"email","password","password_confirmation"},
     *       @OA\Property(property="email", type="string", format="email", example="user@gmail.com"),
     *       @OA\Property(property="password", type="string", format="password", example="string12345"),
     *       @OA\Property(property="password_confirmation", type="string", format="password", example="string12345"),
     *    ),
     * ),
     * @OA\Response(
     *   response=201,
     *   description="Registrasi berhasil, OTP telah dikirim ke email",
     *   @OA\JsonContent(
     *     @OA\Property(property="temp_token", type="string", example="temporary_token_string"),
     *    @OA\Property(property="email", type="string", example="user@gmail.com"),
     *    @OA\Property(property="otp", type="string", example="1234")
     *  )
     * ),
     * @OA\Response(
     *   response=400,
     *   description="Bad Request - Invalid input data",
     *   @OA\JsonContent(
     *     @OA\Property(property="message", type="string", example="The given data was invalid."),
     *     @OA\Property(property="errors", type="object",
     *       @OA\Property(property="email", type="array",
     *         @OA\Items(type="string", example="The email has already been taken.")
     *       )
     *     )
     *   )
     * )
     * )
     */
    public function initiateRegister(InitiateRegiterRequest $request)
    {
        $validatedData = $request->validated();

        $authService = new AuthService;
        $otpService = new OtpService;

        DB::beginTransaction();
        try{
            $regInit = $authService->registerToCache($validatedData);
            $otp = $otpService->createOtp($request['email'], OtpTypeEnum::EMAIL->value, VerificationTypeEnum::REGISTER->value);
            DB::commit();
        } catch (Exception $e){
            DB::rollBack();
            throw $e;
        }
        
        return response()->json([
            "temp_token" => $regInit['temp_token'],
            "email" => $regInit['email'],
            "otp" => $otp['otpCode'] // For testing/debug purposes only
        ], 201);
    }
    
    /**
     * Verify email using OTP during registration
     * @OA\Patch(
     * path="api/register/verify",
     * operationId="verifyEmail",
     * summary="Verifikasi email menggunakan OTP saat registrasi",
     * description="Endpoint untuk memverifikasi email user baru dengan menggunakan OTP yang telah dikirimkan ke email tersebut selama proses registrasi.",
     * tags={"Authentication"},
     * @OA\RequestBody(
     *    required=true,
     *    description="Data verifikasi email",
     *    @OA\JsonContent(
     *       required={"temp_token","otp_code"},
     *       @OA\Property(property="temp_token", type="string", example="temporary_token_string"),
     *       @OA\Property(property="otp_code", type="string", example="1234"),
     *    ),
     * ),
     * @OA\Response(
     *   response=200,
     *   description="Email berhasil diverifikasi",
     *   @OA\JsonContent(
     *     @OA\Property(property="message", type="string", example="Registrasi berhasil. Email terverifikasi."),
     *     @OA\Property(property="token", type="string", example="auth_token_string")
     *  )
     * ),
     * @OA\Response(
     *   response=400,
     *   description="Bad Request - Invalid OTP or temp token",
     *   @OA\JsonContent(
     *     @OA\Property(property="message", type="string", example="Invalid OTP or temporary token."),
     *   )
     * )
     * )
     */
    public function verifyEmail(StoreOtpRequest $request)
    {
        $validatedData = $request->validated();

        $authService = new AuthService;
        $user = $authService->verifyRegister(
            $validatedData['temp_token'],
            $validatedData['otp_code']
        );

        return response()->json([
            "message" => "Registrasi berhasil. Email terverifikasi.",
            "token" => $user['token']
        ], 200);
    }

    /**
     * Resend OTP for email verification during registration
     * @OA\Patch(
     * path="api/register/resend-otp",
     * operationId="resendOtp",
     * summary="Kirim ulang OTP untuk verifikasi email saat registrasi",
     * description="Endpoint untuk mengirim ulang OTP ke email user baru selama proses registrasi jika OTP sebelumnya tidak diterima atau hilang.",
     * tags={"Authentication"},
     * @OA\RequestBody(
     *    required=true,
     *    description="Data untuk mengirim ulang OTP",
     *    @OA\JsonContent(
     *       required={"temp_token"},
     *       @OA\Property(property="temp_token", type="string", example="temporary_token_string"),
     *    ),
     * ),
     * @OA\Response(
     *   response=200,
     *   description="OTP berhasil dikirim ulang ke email",
     *   @OA\JsonContent(
     *     @OA\Property(property="message", type="string", example="OTP berhasil terkirim kembali."),
     *     @OA\Property(property="otp_code", type="string", example="1234")
     *  )
     * ),
     * @OA\Response(
     *   response=400,
     *   description="Bad Request - Invalid temp token",
     *   @OA\JsonContent(
     *     @OA\Property(property="message", type="string", example="Invalid temporary token."),
     *   )
     * )
     * )
     */
    public function resendOtp(UpdateOtpRequest $request)
    {
        $validatedData = $request->validated();

        $authService = new AuthService;

        $result = $authService->resendOtpRegister(
            $validatedData['temp_token']
        );

        return response()->json([
            "message" => "OTP berhasil terkirim kembali.",
            "otp_code" => $result['otpCode']
        ], 200);
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
            $otp = $otpService->createOtp($user->email, OtpTypeEnum::EMAIL->value, VerificationTypeEnum::FORGOT_PASSWORD->value, 10, $user->id);
            $data = [
                'identifier' => $user->email,
                'identifier_type' => OtpTypeEnum::EMAIL->value,
                'verification_type' => VerificationTypeEnum::FORGOT_PASSWORD->value,
            ];
            $caching = $otpService->storeOtpToCache(VerificationTypeEnum::FORGOT_PASSWORD->value, $data);
            DB::commit();
        } catch (Exception $e){
            DB::rollBack();
            throw $e;
        }
        
        return response()->json([
            "message" => "OTP untuk reset password telah dikirim ke email Anda.",
            "otp" => $otp['otpCode'],
            "temp_token" => $caching['temp_token']
        ], 200);
    }

    
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

    /**
     * Verify OTP for forgot password
     * @OA\Patch(
     * path="api/forgot-password/verify",
     * operationId="verifyForgotPassword",
     * summary="Verifikasi OTP untuk lupa password",
     * description="Endpoint untuk memverifikasi OTP yang dikirimkan ke email user saat proses lupa password.",
     * tags={"Authentication"},
     * @OA\RequestBody(
     *    required=true,
     *    description="Data verifikasi OTP lupa password",
     *    @OA\JsonContent(
     *       required={"temp_token","otp_code"},
     *       @OA\Property(property="temp_token", type="string", example="temporary_token_string"),
     *       @OA\Property(property="otp_code", type="string", example="1234"),
     *    ),
     * ),
     * @OA\Response(
     *   response=200,
     *   description="OTP berhasil diverifikasi untuk reset password",
     *   @OA\JsonContent(
     *     @OA\Property(property="status", type="string", example="success"),
     *     @OA\Property(property="message", type="string", example="OTP verified successfully."),
     *     @OA\Property(property="token", type="string", example="reset_token_string")
     *  )
     * ),
     * @OA\Response(
     *   response=400,
     *   description="Bad Request - Invalid OTP or temp token",
     *   @OA\JsonContent(
     *     @OA\Property(property="message", type="string", example="Invalid OTP or temporary token."),
     *   )
     * )
     * )
     */
    public function verifyForgotPassword(VerifyOtpRequest $request)
    {
        $request->validated();
        $otpService = new OtpService;
        $verify = $otpService->verifyOtp($request->otp_code, VerificationTypeEnum::FORGOT_PASSWORD->value, $request->temp_token);
        $tokenReset = Str::random(15);
        Cache::put('auth:reset-password:' . $tokenReset, ["email" => $verify['identifier']], 1800);

        return response()->json([
            "status" => $verify['status'],
            "message" => $verify['message'],
            "token" => $tokenReset
        ], $verify["code"]);
    }

    /**
     * Reset user password
     * @OA\Patch(
     * path="api/reset-password",
     * operationId="resetPassword",
     * summary="Reset password user",
     * description="Endpoint untuk mereset password user setelah OTP lupa password berhasil diverifikasi.",
     * tags={"Authentication"},
     * @OA\RequestBody(
     *    required=true,
     *    description="Data reset password",
     *    @OA\JsonContent(
     *       required={"token","password","password_confirmation"},
     *       @OA\Property(property="token", type="string", example="reset_token_string"),
     *       @OA\Property(property="password", type="string", format="password", example="newpassword123"),
     *       @OA\Property(property="password_confirmation", type="string", format="password", example="newpassword123"),
     *    ),
     * ),
     * @OA\Response(
     *   response=200,
     *   description="Password berhasil direset",
     *   @OA\JsonContent(
     *     @OA\Property(property="message", type="string", example="Password berhasil direset.")
     *  )
     * ),
     * @OA\Response(
     *   response=400,
     *   description="Bad Request - Invalid token or input data",
     *   @OA\JsonContent(
     *     @OA\Property(property="message", type="string", example="Invalid reset token."),
     *   )
     * )
     * )
     */
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
