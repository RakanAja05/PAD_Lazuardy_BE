<?php

namespace App\Http\Controllers;

use App\Enums\Role;
use App\Models\User;
use Exception;
use Illuminate\Support\Facades\Auth; 
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Enum;
use Laravel\Socialite\Facades\Socialite;

class SocialAuthController extends Controller
{
    // Redirect user ke penyedia autentikasi
    public function redirectToProvider(string $provider)
    {
        return Socialite::driver($provider)->redirect();
    }

    // Menghandle callback dari provider
    public function handleProviderCallback(string $provider)
    {
        try {
            dd($provider);
            $socialiteUser = Socialite::driver($provider)->user();
            $providerIdColumn = $provider . '_id';

            $user = User::where('email', $socialiteUser->getEmail())
                        ->orWhere($providerIdColumn, $socialiteUser->getId())
                        ->first();

            $loggedUser = null;

            if($user) {
                if (empty($user->$providerIdColumn)) {
                    $user->{$providerIdColumn} = $socialiteUser->getId();
                    $user->save();
                    $loggedUser = $user;
                }
            } else {
                $newUser = User::create([
                    'name' => $socialiteUser->getName(),
                    'email' => $socialiteUser->getEmail(),
                    'role' => 'student',
                    $providerIdColumn => $socialiteUser->getId(),
                    'password' => Hash::make(str()->random(16)), 
                    'email_verified_at' => now(),
                ]);
                    $loggedUser = $user;
            }
            
            Auth::login($loggedUser, true);

        } catch (Exception $e) {
            dd($e->getMessage());
        }
    }
}
