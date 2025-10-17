<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreOtpRequest;
use App\Http\Requests\UpdateOtpRequest;
use App\Mail\OtpEmail;
use App\Models\Otp;
use App\Services\OtpService;
use Illuminate\Support\Facades\Mail;

class VerificationController extends Controller
{
    /**
     * Send OTP to user
     * @OA\Post(
     * path="/api/otp/send",
     * operationId="sendOtp",
     * tags={"OTP"},
     * summary="Mengirim OTP ke user",
     * description="Mengirim OTP ke user via email atau telephone_number",
     * @OA\RequestBody(
     * required=true,
     * description="Pass user credentials",
     * @OA\JsonContent(
     * required={"identifier", "identifier_type","verification_type"},
     * @OA\Property(property="identifier", type="string", example="user@example.com", description="Email atau nomor telepon user."),
     * @OA\Property(property="identifier_type", type="string", example="email", description="Tipe identifier: email atau telephone_number."),
     * @OA\Property(property="verification_type", type="string", example="registration", description="Tipe verifikasi: registration, password_reset, dll."),
     * ),
     * ),
     * @OA\Response(
     * response=200,
     * description="Success",
     * @OA\JsonContent(
     * @OA\Property(property="message", type="string", example="OTP berhasil terkirim."),
     * @OA\Property(property="otp_code", type="string", example="ABC123", description="Kode OTP yang dibuat (Hanya untuk testing/debug)."),
     * )
     * ),
     * @OA\Response(
     * response=400,
     * description="Bad Request",
     * @OA\JsonContent(
     * @OA\Property(property="message", type="string", example="Invalid request data."),
     * )
     * ),
     * @OA\Response(
     * response=404,
     * description="Not Found",
     * @OA\JsonContent(
     * @OA\Property(property="message", type="string", example="Email atau nomor hp tidak ditemukan."),
     * )
     * ),
     * @OA\Response(
     * response=403,
     * description="Forbidden / Unauthorized",
     * @OA\JsonContent(
     * @OA\Property(property="message", type="string", example="Unauthorized."),
     * ),
     * ),
     * )
     */
    public function sendOtp(StoreOtpRequest $request)
    {
        $user = $request->user();
        $request->validated();
        $identifierType = $request->identifier_type;
        $identifier = $user->{$identifierType};

        if (!$identifier) {
            return response()->json(['message' => 'Identifier not found for user.'], 404);
        }

        $otpService = new OtpService();
        $result = $otpService->createOtp($user->id, $identifier, $identifierType, $request->verification_type);
        $otp = $result['otp'];
        $otpCode = $result['otpCode'];

        Mail::to($user->email)->send(new OtpEmail($otpCode));

        return response()->json(['message' => 'OTP sent successfully.'], 200);
    }

    /**
     * Verify OTP
     * @OA\Patch(
     * path="/api/otp/verify",
     * operationId="verifyOtp",
     * tags={"OTP"},
     * summary="Verifikasi OTP",
     * description="Verifikasi OTP yang dikirim ke user via email atau telephone_number.",
     * @OA\RequestBody(
     * required=true,
     * description="Data yang diperlukan untuk verifikasi OTP",
     * @OA\JsonContent(
     * required={"identifier", "identifier_type","verification_type","code"},
     * @OA\Property(property="identifier", type="string", example="user@example.com", description="Email atau nomor telepon user."),
     * @OA\Property(property="identifier_type", type="string", example="email", description="Tipe identifier: email atau telephone_number."),
     * @OA\Property(property="verification_type", type="string", example="registration", description="Tipe verifikasi: registration, password_reset, dll."),
     * @OA\Property(property="code", type="string", example="123456", description="Kode OTP yang diterima user."),
     * ),
     * ),
     * @OA\Response(
     * response=200,
     * description="Success",
     * @OA\JsonContent(
     * @OA\Property(property="message", type="string", example="OTP is valid."),
     * ),
     * ),
     * @OA\Response(
     * response=400,
     * description="Bad Request / Invalid Code",
     * @OA\JsonContent(
     * @OA\Property(property="message", type="string", example="Invalid OTP code."),
     * ),
     * ),
     * @OA\Response(
     * response=404,
     * description="Not Found / Expired OTP",
     * @OA\JsonContent(
     * @OA\Property(property="message", type="string", example="No valid OTP found or OTP has expired."),
     * ),
     * ),
     * @OA\Response(
     * response=403,
     * description="Forbidden / Unauthorized",
     * @OA\JsonContent(
     * @OA\Property(property="message", type="string", example="Unauthorized."),
     * ),
     * ),
     * )
     */
    public function verifyOtp(UpdateOtpRequest $request)
    {
        $user = $request->user();
        $request->validated();
        $identifierType = $request->identifier_type;
        $identifier = $user->{$identifierType};

        $otpService = new OtpService();
        $result = $otpService->verifyOtp($user, $identifier, $identifierType, $request->verification_type, $request->code);

        if ($result['status'] === 'success') {
            return response()->json(['message' => 'OTP is valid.'], 200);
        } else {
            return response()->json(['message' => $result['message']], $result['code']);
        }
    }

    /**
     * Resend OTP
     * @OA\Post(
     * path="/api/otp/resend",
     * operationId="resendOtp",
     * tags={"OTP"},
     * summary="resend OTP",
     * description="Resend OTP yang dikirim ke user via email atau telephone_number.",
     * @OA\RequestBody(
     * required=true,
     * description="Data yang diperlukan untuk mengirim ulang OTP",
     * @OA\JsonContent(
     * required={"identifier_type","verification_type"},
     * @OA\Property(property="identifier_type", type="string", example="email", description="Tipe identifier: email atau telephone_number."),
     * @OA\Property(property="verification_type", type="string", example="registration", description="Tipe verifikasi: registration, password_reset, dll."),
     * ),
     * ),
     * @OA\Response(
     * response=200,
     * description="Success",
     * @OA\JsonContent(
     * @OA\Property(property="message", type="string", example="OTP berhasil terkirim."),
     * @OA\Property(property="otp_code", type="string", example="ABC123", description="Kode OTP yang dibuat (Hanya untuk testing/debug)."),
     * )
     * ),
     * @OA\Response(
     * response=400,
     * description="Bad Request",
     * @OA\JsonContent(
     * @OA\Property(property="message", type="string", example="Invalid request data."),
     * )
     * ),
     * @OA\Response(
     * response=404,
     * description="Not Found",
     * @OA\JsonContent(
     * @OA\Property(property="message", type="string", example="No valid OTP found to resend."),
     * )
     * ),
     * @OA\Response(
     * response=403,
     * description="Forbidden / Unauthorized",
     * @OA\JsonContent(
     * @OA\Property(property="message", type="string", example="Unauthorized."),
     * ),
     * ),
     * )
     */
    public function resendOtp(StoreOtpRequest $request)
    {
        $user = $request->user();
        $request->validated();
        $identifierType = $request->identifier_type;
        $identifier = $user->{$identifierType};

        $otp = Otp::byIdentifier($identifier, $identifierType)
                    ->byVerificationType($request->verification_type)
                    ->valid()
                    ->latest()
                    ->first();

        if (!$otp) {
            return response()->json(['message' => 'No valid OTP found to resend.'], 404);
        }

        $otpService = new OtpService();
        $resend = $otpService->resendOtp($otp);
        $otpCode = $resend['otpCode'];

        Mail::to($user->email)->send(new OtpEmail($otpCode));

        return response()->json(['message' => 'OTP sent successfully.'], 200);
    }
}
