<?php

namespace App\Services;

use App\Mail\OtpEmail;
use App\Models\Otp;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Mail;

class OtpService
{
    /**
     * Generate a random OTP code
     */
    public function generateOtp($length = 4)
    {
        return str_pad(random_int(0, pow(10, $length) - 1), $length, '0', STR_PAD_LEFT);
    }

    public function createOtp($identifier, $identifierType, $verificationType, $expiryMinutes = 10,$userId = null)
    {
        $code = $this->generateOtp();
        $expiredAt = Carbon::now()->addMinutes($expiryMinutes);

        $data = [
            "identifier" => $identifier,
            "identifier_type" => $identifierType,
            "code" => hash('sha256',$code),
            "verification_type" => $verificationType,
            "expired_at" => $expiredAt
        ];

        if($userId){
            $data["user_id"] = $userId;
        }
        
        $createData = Otp::create($data);

        // Mail::to($identifier)->send(new OtpEmail($code));

        return [
            "otp" => $createData,
            "otpCode" => $code
        ];
    }

    public function verifyOtp($code, $identifier, $identifierType, $verificationType, $userId = null)
    {
        $otp = Otp::byIdentifier($identifier, $identifierType)
                    ->byVerificationType($verificationType)
                    ->valid()
                    ->latest()
                    ->first();
        
        if(!$otp)
        {
            return [
                "status" => "error",
                "message" => "No valid OTP found or OTP has expired",
                "code" => 404
            ];
        }

        if(hash('sha256', $code) == $otp->code)
        {
            $otp->markAsUsed();
            return [
                'status' => 'success',
                'message' => 'OTP is valid.',
                'code' => 200
            ];
        } else {
            $otp->incrementAttempts();
            return [
                'status' => 'error',
                'message' => 'Invalid OTP code.',
                'code' => 400
            ];
        }
    }

    public function resendOtp($otp, $expiryMinutes = 10)
    {
        $code = $this->generateOtp();
        $otp->code = hash('sha256', $code);
        $otp->expired_at = Carbon::now()->addMinutes($expiryMinutes);
        $otp->attempts = 0;
        $otp->is_used = false;
        $otp->save();

        return ["otp" => $otp, "otpCode" => $code];
    }
}
