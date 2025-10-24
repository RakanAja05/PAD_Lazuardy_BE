<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Laravel\Socialite\Facades\Socialite;
use App\Models\User;
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
     */
    public function handleGoogleCallback()
    {
        try {
            // Ambil data dari Google
            $googleUser = Socialite::driver('google')->stateless()->user();

            // Cari user berdasarkan google_id atau email
            $user = User::where('google_id', $googleUser->getId())
                        ->orWhere('email', $googleUser->getEmail())
                        ->first();

            // Kalau belum ada user, buat baru
            if (! $user) {
                $user = User::create([
                    'name' => $googleUser->getName(),
                    'email' => $googleUser->getEmail(),
                    'google_id' => $googleUser->getId(),
                    'password' => Hash::make(uniqid()), // password random
                    'role' => 'student', // default role
                ]);
            } else {
                // Jika user ditemukan berdasarkan email tapi belum punya google_id, tautkan akun
                if (empty($user->google_id)) {
                    $user->google_id = $googleUser->getId();
                    $user->save();
                }
            }

            // Login dan buat token Sanctum
            Auth::login($user);
            $token = $user->createToken('auth_token')->plainTextToken;

            // Kirim respons JSON (kalau untuk API)
            // Jika FRONTEND_URL diset, redirect ke frontend dengan token (SPA-friendly)
            $frontend = config('app.frontend_url') ?? env('FRONTEND_URL');
            if ($frontend && ! request()->wantsJson()) {
                // Redirect ke frontend; frontend bertanggung jawab menyimpan token
                $redirectUrl = rtrim($frontend, '/') . '/auth/callback?token=' . urlencode($token);
                return redirect()->away($redirectUrl);
            }

            return response()->json([
                'message' => 'Login Google berhasil',
                'user' => $user,
                'token' => $token,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Gagal login dengan Google',
                'message' => $e->getMessage(),
            ], 500);
        }
    }
}
