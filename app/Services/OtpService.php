<?php

namespace App\Services;

use App\Enums\OtpTypeEnum;
use App\Models\Otp;
use Exception;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class OtpService
{
    /**
     * Generate a random OTP code
     */
    public function generateOtp($length = 4)
    {
        return str_pad(random_int(0, pow(10, $length) - 1), $length, '0', STR_PAD_LEFT);
    }

    public function createOtp($identifier, $identifierType, $verificationType, $userId = null, $expiryMinutes = 10)
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
        
        try {
            $createData = Otp::create($data);
        } catch (Exception $e) {
            throw $e;
        }

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

            if($identifierType == OtpTypeEnum::EMAIL)
            {
                
            }
        }
    }




    


//     public function verifyOtp($user, $identifier, $identifierType, $verificationType, $code)
//     {
//         $otp = Otp::byIdentifier($identifier, $identifierType)
//             ->byVerificationType($verificationType)
//             ->valid()
//             ->latest()
//             ->first();

//         if (!$otp) {
//             return [
//                 'status' => 'error',
//                 'message' => 'No valid OTP found or OTP has expired.',
//                 'code' => 404
//             ];
//         }

//         if (Hash::check($code, $otp->code)) {
//             $otp->markAsUsed();
            
//             if ($identifierType === 'email') {
//                 $user->email_verified_at = now();
//                 $user->save();
//             }
//             return [
//                 'status' => 'success',
//                 'message' => 'OTP is valid.',
//                 'code' => 200
//             ];
//         } else {
//             $otp->incrementAttempts();
//             return [
//                 'status' => 'error',
//                 'message' => 'Invalid OTP code.',
//                 'code' => 400
//             ];
//         }
//     }

//     public function resendOtp($otp, $expiryMinutes = 10)
//     {
//         $code = $this->generateOtp();
//         $otp->code = Hash::make($code);
//         $otp->expired_at = Carbon::now()->addMinutes($expiryMinutes);
//         $otp->attempts = 0;
//         $otp->is_used = false;
//         $otp->save();

//         return ["otp" => $otp, "otpCode" => $code];
//     }

//     public function invalidateOtp($otp)
//     {
//         $otp->markAsUsed();
//     }

//     public function cleanupExpiredOtps()
//     {
//         Otp::where('expired_at', '<', Carbon::now())->delete();
//     }
}
