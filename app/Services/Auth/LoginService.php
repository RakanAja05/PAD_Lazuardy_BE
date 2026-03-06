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
                'status' => 'error',
                'message' => 'Email atau password salah',
                'errors' => [
                    'credentials' => 'invalid',
                ],
            ], 401);
        }

        $user = $request->user();
        $user->tokens()->delete();
        $token = $user->createToken('auth_token')->plainTextToken;

        return new ResponseDTO([
            'status' => 'success',
            'message' => 'Login berhasil',
            'data' => [
                'token' => $token,
                'user' => $user,
            ],
        ], 200);
    }

    public function logout(Request $request): ResponseDTO
    {
        $request->user()->currentAccessToken()?->delete();

        return new ResponseDTO([
            'status' => 'success',
            'message' => 'Logout berhasil',
            'data' => [],
        ], 200);
    }

    public function me(Request $request): ResponseDTO
    {
        return new ResponseDTO([
            'status' => 'success',
            'message' => 'Berhasil mengambil profil',
            'data' => [
                'user' => $request->user(),
            ],
        ], 200);
    }
}
