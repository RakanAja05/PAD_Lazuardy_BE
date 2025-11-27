<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Laravel\Socialite\Facades\Socialite;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class GoogleController extends Controller
{
    /**
     * Redirect ke halaman login Google
     */
    public function redirectToGoogle()
    {
        return Socialite::driver('google')->redirect();
    }

    /**
     * Callback dari Google
     * Return JSON response langsung (Direct API response)
     */
    public function handleGoogleCallback()
    {
        try {
            // Ambil data dari Google
            $googleUser = Socialite::driver('google')->stateless()->user();

            // Cek apakah email sudah terdaftar di sistem (by email saja)
            $existingUser = User::where('email', $googleUser->getEmail())->first();

            // ========== SKENARIO 1: USER SUDAH TERDAFTAR ==========
            if ($existingUser) {
                // Update google_id jika user terdaftar manual tapi belum pernah login via Google
                if (empty($existingUser->google_id)) {
                    $existingUser->google_id = $googleUser->getId();
                    $existingUser->save();
                }

                // Hapus token lama
                $existingUser->tokens()->delete();

                // Buat token Sanctum baru
                $token = $existingUser->createToken('auth_token')->plainTextToken;

                return response()->json([
                    'status' => 'success',
                    'type' => 'login',
                    'message' => 'Login berhasil',
                    'token' => $token,
                    'user' => [
                        'id' => $existingUser->id,
                        'name' => $existingUser->name,
                        'email' => $existingUser->email,
                        'role' => $existingUser->role,
                    ]
                ], 200);
            }

            // ========== SKENARIO 2: USER BELUM TERDAFTAR ==========
            return response()->json([
                'status' => 'success',
                'type' => 'register',
                'message' => 'User belum terdaftar, silakan lengkapi data',
                'google_data' => [
                    'google_id' => $googleUser->getId(),
                    'name' => $googleUser->getName(),
                    'email' => $googleUser->getEmail(),
                    'avatar' => $googleUser->getAvatar(),
                ]
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal login dengan Google: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Complete registration dengan data Google
     * Endpoint untuk Vue setelah user melengkapi form register
     */
    public function completeGoogleRegistration(Request $request)
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
            // Buat user baru dengan data dari Google + form
            $user = User::create([
                'google_id' => $validated['google_id'],
                'name' => $validated['name'],
                'email' => $validated['email'],
                'role' => $validated['role'],
                'telephone_number' => $validated['telephone_number'] ?? null,
                'date_of_birth' => $validated['date_of_birth'] ?? null,
                'gender' => $validated['gender'] ?? null,
                'profile_photo_url' => $validated['profile_photo_url'] ?? null,
                'email_verified_at' => now(), // Email sudah terverifikasi oleh Google
                'password' => Hash::make(uniqid()), // Password random (tidak dipakai)
            ]);

            // Buat token Sanctum
            $token = $user->createToken('auth_token')->plainTextToken;

            return response()->json([
                'message' => 'Registrasi berhasil',
                'user' => $user,
                'token' => $token,
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Gagal menyelesaikan registrasi',
                'message' => $e->getMessage(),
            ], 500);
        }
    }
}
