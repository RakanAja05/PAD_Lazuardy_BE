<?php

namespace App\Services;

use App\Models\Otp;
use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Hash;

class OtpService
{
    /**
     * Generate a random OTP code
     */
    public function generateOtp($length = 6)
    {
        return str_pad(random_int(0, pow(10, $length) - 1), $length, '0', STR_PAD_LEFT);
    }
    
    public function createOtp($userId, $identifier, $identifierType, $verificationType, $expiryMinutes = 10)
    {
        $code = $this->generateOtp();
        $expiredAt = Carbon::now()->addMinutes($expiryMinutes);
        $createData =  Otp::create([
            'user_id' => $userId,
            'identifier' => $identifier,
            'identifier_type' => $identifierType,
            'code' => Hash::make($code),
            'verification_type' => $verificationType,
            'expired_at' => $expiredAt,
        ]);

        return [
            "otp" => $createData,
            "otpCode" => $code
        ];
    }

    public function verifyOtp($user, $identifier, $identifierType, $verificationType, $code)
    {
        $otp = Otp::byIdentifier($identifier, $identifierType)
            ->byVerificationType($verificationType)
            ->valid()
            ->latest()
            ->first();

        if (!$otp) {
            return [
                'status' => 'error',
                'message' => 'No valid OTP found or OTP has expired.',
                'code' => 404
            ];
        }

        if (Hash::check($code, $otp->code)) {
            $otp->markAsUsed();
            // Update email_verified_at
            if ($identifierType === 'email') {
                $user->email_verified_at = now();
                $user->save();
            }
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
        $otp->code = Hash::make($code);
        $otp->expired_at = Carbon::now()->addMinutes($expiryMinutes);
        $otp->attempts = 0;
        $otp->is_used = false;
        $otp->save();

        return ["otp" => $otp, "otpCode" => $code];
    }

    public function invalidateOtp($otp)
    {
        $otp->markAsUsed();
    }

    public function cleanupExpiredOtps()
    {
        Otp::where('expired_at', '<', Carbon::now())->delete();
    }
}
