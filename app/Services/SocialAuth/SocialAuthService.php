<?php

namespace App\Services\SocialAuth;

use App\Models\User;
use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;

class SocialAuthService
{
    public function redirectToProvider(string $provider): RedirectResponse
    {
        return Socialite::driver($provider)->redirect();
    }

    public function handleProviderCallback(string $provider)
    {
        try {
            $socialiteUser = Socialite::driver($provider)->stateless()->user();
            $providerIdColumn = $provider . "_id";

            $user = User::where('email', $socialiteUser->getEmail())
                ->orWhere($providerIdColumn, $socialiteUser->getId())
                ->first();

            $loggedUser = null;

            if ($user) {
                if (empty($user->$providerIdColumn)) {
                    $user->{$providerIdColumn} = $socialiteUser->getId();
                    $user->save();
                }
                $loggedUser = $user;
            } else {
                $userEmail = $socialiteUser->getEmail() ?? $socialiteUser->getId() . '@' . $provider . '.local';
                $tempToken = Str::random(40);
                Cache::put('social:register:' . $tempToken, [
                    'provider' => $provider,
                    'provider_id' => $socialiteUser->getId(),
                    'email' => $userEmail,
                    'name' => $socialiteUser->getName(),
                    'avatar' => $socialiteUser->getAvatar(),
                ], 1800);

                $frontend = config('app.frontend_url') ?? env('FRONTEND_URL');
                $payload = json_encode([
                    'provider' => $provider,
                    'email' => $userEmail,
                    'temp_token' => $tempToken,
                ]);

                if ($frontend) {
                    $redirectUrl = rtrim($frontend, '/') . '/register?social_data=' . urlencode($payload);
                    return redirect()->away($redirectUrl);
                }

                return response()->json([
                    'status' => 'success',
                    'message' => 'Akun belum terdaftar, silakan lengkapi data',
                    'data' => [
                        'type' => 'register',
                        'provider' => $provider,
                        'email' => $userEmail,
                        'temp_token' => $tempToken,
                    ],
                ], 200);
            }

            $token = $loggedUser->createToken('auth_token')->plainTextToken;

            return response()->json([
                'status' => 'success',
                'message' => 'Login menggunakan ' . ucfirst($provider) . ' berhasil.',
                'data' => [
                    'user' => $loggedUser,
                    'token' => $token,
                    'token_type' => 'Bearer',
                ],
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal otentikasi melalui ' . ucfirst($provider) . '.',
                'errors' => [
                    'detail' => $e->getMessage(),
                ],
            ], 500);
        }
    }
}
