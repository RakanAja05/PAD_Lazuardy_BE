<?php

namespace App\Services;

use App\Enums\OtpIdentifierEnum;
use App\Enums\OtpTypeEnum;
use App\Models\User;
use Exception;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str; 

class AuthService
{
    public function registerUser(array $data)
    {
        $data['email_verified_at'] = now();
        DB::beginTransaction();
        try {
            $user = User::create($data);
            $token = $user->createToken('auth_token')->plainTextToken;

            DB::commit();
            return [
                'user' => $user,
                'token' => $token
            ];
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function storeToCache(array $data)
    {
        $temp_token = Str::random(15);
        Cache::put('registration:pending:' . $temp_token, $data, 1800);
        
        return [
            'temp_token' => $temp_token,
            'email' => $data['email']
        ];
    }

    public function verifyRegister(string $temp_token, string $otpCode)
    {
        $cache_key = 'registration:pending:' . $temp_token;
        $cache_data = Cache::get($cache_key);

        if(!$cache_data)
        {
            throw new Exception("Sesi registrasi tidak ditemukan atau sudah kadaluarsa");
        }

        $otpService = new OtpService;
        DB::beginTransaction();
        try {
            $otpService->checkOtp(
                $otpCode, 
                $cache_data['email'], 
                OtpIdentifierEnum::EMAIL->value, 
                OtpTypeEnum::REGISTER->value
            );
            $resultUser = $this->registerUser($cache_data);
            DB::commit();

            Cache::forget($cache_key);

            return $resultUser;
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function resendOtpRegister(string $temp_token)
    {
        $cache_key = 'registration:pending:' . $temp_token;
        $cache_data = Cache::get($cache_key);

        if(!$cache_data)
        {
            throw new Exception("Sesi registrasi tidak ditemukan atau sudah kadaluarsa");
        }

        $otpService = new OtpService;
        $result = $otpService->resendOtp(
            $cache_data['email'], 
            OtpIdentifierEnum::EMAIL->value, 
            OtpTypeEnum::REGISTER->value
        );

        return $result;
    }
}
