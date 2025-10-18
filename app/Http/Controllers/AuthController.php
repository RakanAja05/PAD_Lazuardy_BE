<?php

namespace App\Http\Controllers;

use App\Enums\OtpTypeEnum;
use App\Enums\VerificationTypeEnum;
use App\Http\Requests\GetOtpRequest;
use App\Http\Requests\StoreUserRequest;
use App\Services\AuthService;
use App\Services\OtpService;
use Exception;
use Illuminate\Support\Facades\DB;

class AuthController extends Controller
{
    public function initiateRegister(StoreUserRequest $request)
    {
        $validatedData = $request->validated();

        $authService = new AuthService;
        $otpService = new OtpService;

        DB::beginTransaction();
        try{
            $regInit = $authService->initiateRegister($validatedData);
            $otp = $otpService->createOtp($regInit['email'], OtpTypeEnum::EMAIL, VerificationTypeEnum::REGISTRATION);
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

    public function verifyEmail(GetOtpRequest $request)
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
}
