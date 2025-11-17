<?php

namespace App\Services;

use App\Enums\OtpIdentifierEnum;
use App\Enums\OtpTypeEnum;
use App\Enums\VerificationTypeEnum;
use App\Jobs\SendOtpMailJob;
use App\Mail\OtpEmail;
use App\Models\Otp;
use Exception;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str; 

class OtpService
{
    /**
     * Generate a random OTP code
     */
    public function generateOtp($length = 4)
    {
        return str_pad(random_int(0, pow(10, $length) - 1), $length, '0', STR_PAD_LEFT);
    }

    /**
     * OTP Caching
     * RequestData(
     * verificationType,
     * identifier
     * }
     * )
     */
    public function storeOtpToCache($verificationType, $data)
    {
        $token = Str::random(15);
        Cache::put('otp:' . $verificationType . ':' . $token, $data, 1800);
        
        return [
            'token' => $token,
        ];
    }

    public function getOtpFromCache($verificationType, $temp_token)
    {
        $cache_key = 'otp:' . $verificationType . ':' . $temp_token;
        $cache_data = Cache::get($cache_key);

        if(!$cache_data)
        {
            switch($verificationType)
            {
                case OtpTypeEnum::REGISTER->value:
                    throw new Exception("Sesi registrasi tidak ditemukan atau sudah kadaluarsa");
                case OtpTypeEnum::FORGOT_PASSWORD->value:
                    throw new Exception("Sesi lupa password tidak ditemukan atau sudah kadaluarsa");
                default:
                    throw new Exception("Sesi OTP tidak ditemukan atau sudah kadaluarsa");
            }
        }

        return $cache_data;
    }


    /**
     * Create and send an OTP
     */
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
        
        $createData = Otp::create($data);

        switch($identifierType)
        {
            case OtpIdentifierEnum::EMAIL->value:
                dispatch(new SendOtpMailJob($identifier, $code));
                break;
        }

        return [
            "otp" => $createData,
            "code" => $code
        ];
    }


    /**
     * verify OTP
     */
    public function verifyOtp($otpCode, $verificationType, $temp_token)
    {
        $cache_data = $this->getOtpFromCache($verificationType, $temp_token);
        $otp = $this->checkOtp(
            $otpCode,
            $cache_data['identifier'],
            $cache_data['identifier_type'],
            $cache_data['verification_type'],
        );

        return $otp;
    }

    public function checkOtp($code, $identifier, $identifierType, $verificationType)
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
                "identifier" => $identifier,
                "code" => 404
            ];
        }

        if(hash('sha256', $code) == $otp->code)
        {
            $otp->markAsUsed();
            return [
                'status' => 'success',
                'message' => 'OTP is valid.',
                'identifier' => $identifier,
                'code' => 200
            ];
        } else {
            $otp->incrementAttempts();
            return [
                'status' => 'error',
                'message' => 'Invalid OTP code.',
                "identifier" => $identifier,
                'code' => 400
            ];
        }
    }

    /**
     * resend OTP
     */
    public function resendOtp($identifier, $identifierType, $verificationType, $expiryMinutes = 10)
    {
        $code = $this->generateOtp();

        $otp = Otp::byIdentifier($identifier, $identifierType)
                    ->byVerificationType($verificationType)
                    ->valid()
                    ->latest()
                    ->first();

        DB::beginTransaction();
        try {
            $otp->code = hash('sha256', $code);
            $otp->expired_at = Carbon::now()->addMinutes($expiryMinutes);
            $otp->attempts = 0;
            $otp->is_used = false;
            $otp->save();

            DB::commit();

            switch($identifierType)
            {
                case OtpIdentifierEnum::EMAIL->value:
                    dispatch(new SendOtpMailJob($identifier, $code));
                    break;
            }

            return [
                "status" => "success", 
                'message' => 'OTP berhasil terkirim',
                "otp_code" => $code, 
                "code" => 200
            ];
            
        } catch (Exception $e) {
            
            DB::rollBack();
            
            return [
                'status' => 'error',
                'message' => 'Failed to resend OTP: ' . $e->getMessage(),
                "otp_code" => null, 
                'code' => 500
            ];
        }
    }
}
