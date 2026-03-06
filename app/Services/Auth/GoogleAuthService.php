<?php

namespace App\Services\Auth;

use App\DTOs\ResponseDTO;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Laravel\Socialite\Facades\Socialite;

class GoogleAuthService
{
    public function redirectToGoogle(): ResponseDTO
    {
        $response = Socialite::driver('google')->redirect();

        return new ResponseDTO($response, $response->getStatusCode());
    }

    public function handleGoogleCallback(): ResponseDTO
    {
        try {
            $googleUser = Socialite::driver('google')->user();

            $existingUser = User::where('email', $googleUser->getEmail())->first();

            if ($existingUser) {
                if (empty($existingUser->google_id)) {
                    $existingUser->google_id = $googleUser->getId();
                    $existingUser->save();
                }

                $existingUser->tokens()->delete();
                $token = $existingUser->createToken('auth_token')->plainTextToken;

                return new ResponseDTO([
                    'status' => 'success',
                    'type' => 'login',
                    'message' => 'Login berhasil',
                    'token' => $token,
                    'user' => [
                        'id' => $existingUser->id,
                        'name' => $existingUser->name,
                        'email' => $existingUser->email,
                        'role' => $existingUser->role,
                    ],
                ], 200);
            }

            return new ResponseDTO([
                'status' => 'success',
                'type' => 'register',
                'message' => 'User belum terdaftar, silakan lengkapi data',
                'google_data' => [
                    'google_id' => $googleUser->getId(),
                    'name' => $googleUser->getName(),
                    'email' => $googleUser->getEmail(),
                    'avatar' => $googleUser->getAvatar(),
                ],
            ], 200);
        } catch (\Exception $e) {
            return new ResponseDTO([
                'status' => 'error',
                'message' => 'Gagal login dengan Google: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function completeGoogleRegistration(Request $request): ResponseDTO
    {
        $validated = $request->validate([
            'google_id' => 'required|string',
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'role' => 'required|in:student,tutor',
            'telephone_number' => 'nullable|string|max:15',
            'date_of_birth' => 'nullable|date',
            'gender' => 'nullable|in:male,female',
            'profile_photo_url' => 'nullable|url',
        ]);

        try {
            $user = User::create([
                'google_id' => $validated['google_id'],
                'name' => $validated['name'],
                'email' => $validated['email'],
                'role' => $validated['role'],
                'telephone_number' => $validated['telephone_number'] ?? null,
                'date_of_birth' => $validated['date_of_birth'] ?? null,
                'gender' => $validated['gender'] ?? null,
                'profile_photo_url' => $validated['profile_photo_url'] ?? null,
                'email_verified_at' => now(),
                'password' => Hash::make(uniqid()),
            ]);

            $token = $user->createToken('auth_token')->plainTextToken;

            return new ResponseDTO([
                'message' => 'Registrasi berhasil',
                'user' => $user,
                'token' => $token,
            ], 201);
        } catch (\Exception $e) {
            return new ResponseDTO([
                'error' => 'Gagal menyelesaikan registrasi',
                'message' => $e->getMessage(),
            ], 500);
        }
    }
}
