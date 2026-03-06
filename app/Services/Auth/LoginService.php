<?php

namespace App\Services\Auth;

use App\DTOs\ResponseDTO;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginService
{
    public function login(Request $request): ResponseDTO
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (!Auth::attempt($credentials)) {
            return new ResponseDTO([
                'message' => 'Email atau password salah',
            ], 401);
        }

        $user = $request->user();
        $user->tokens()->delete();
        $token = $user->createToken('auth_token')->plainTextToken;

        return new ResponseDTO([
            'message' => 'Login berhasil',
            'token' => $token,
            'user' => $user,
        ], 200);
    }

    public function logout(Request $request): ResponseDTO
    {
        $request->user()->currentAccessToken()?->delete();

        return new ResponseDTO([
            'message' => 'Logout berhasil',
        ], 200);
    }

    public function me(Request $request): ResponseDTO
    {
        return new ResponseDTO([
            'user' => $request->user(),
        ], 200);
    }
}
