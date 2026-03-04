<?php

namespace App\Http\Controllers\SocialAuth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Exception;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Cache;
use Laravel\Socialite\Facades\Socialite;

class SocialAuthController extends Controller
{
    // Redirect user ke penyedia autentikasi
    public function redirectToProvider(string $provider)
    {
        return Socialite::driver($provider)->redirect();
    }

    public function handleProviderCallback(string $provider)
    {
        try
        {
            $socialiteUser = Socialite::driver($provider)->stateless()->user();
            $providerIdColumn = $provider . "_id";

            $user = User::where('email', $socialiteUser->getEmail())
                        ->orWhere($providerIdColumn, $socialiteUser->getId())
                        ->first();

            $loggedUser = null;

            if($user)
            {
                if(empty($user->$providerIdColumn))
                {
                    $user->{$providerIdColumn} = $socialiteUser->getId();
                    $user->save();
                }
                $loggedUser = $user;
            } else {
                $userEmail = $socialiteUser->getEmail() ?? $socialiteUser->getId().'@'.$provider.'.local';
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
                    'type' => 'register',
                    'provider' => $provider,
                    'email' => $userEmail,
                    'temp_token' => $tempToken,
                ], 200);
            }

            $token = $loggedUser->createToken('auth_token')->plainTextToken;

            // $frontend = config('app.frontend_url') ?? env('FRONTEND_URL');
            // if ($frontend && ! request()->wantsJson()) {
            //     // Redirect ke frontend; frontend bertanggung jawab menyimpan token
            //     $redirectUrl = rtrim($frontend, '/') . '/auth/callback?token=' . urlencode($token);
            //     return redirect()->away($redirectUrl);
            // }

            return response()->json([
                'user' => $loggedUser,
                'token' => $token,
                'token_type' => 'Bearer',
                'message' => 'Login menggunakan ' . ucfirst($provider) . ' berhasil.',
            ], 200);
        } catch(Exception $e)
        {
            return response()->json([
                'message' => 'Gagal otentikasi melalui ' . ucfirst($provider) . '.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
