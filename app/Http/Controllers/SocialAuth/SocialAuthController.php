<?php

namespace App\Http\Controllers\SocialAuth;

use App\Http\Controllers\Controller;
use App\Services\SocialAuth\SocialAuthService;

class SocialAuthController extends Controller
{
    public function __construct(private readonly SocialAuthService $socialAuthService)
    {
    }

    public function redirectToProvider(string $provider)
    {
        return $this->socialAuthService->redirectToProvider($provider);
    }

    public function handleProviderCallback(string $provider)
    {
        return $this->socialAuthService->handleProviderCallback($provider);
    }
}
