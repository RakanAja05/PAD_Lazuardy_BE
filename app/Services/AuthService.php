<?php

namespace App\Services;

use App\Models\User;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class AuthService
{
    public function registerUser(array $data)
    {
        DB::beginTransaction();
        try {
            $user = User::create([
                'email' => $data['email'],
                'password' => Hash::make($data['password']),
                'role' => $data['role'],
            ]);
        
            $token = $user->createToken('auth_token')->plainTextToken;
            DB::commit();
            
            return [
                'user' => $user,
                'token' => $token
            ];
        } 
        catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
}
