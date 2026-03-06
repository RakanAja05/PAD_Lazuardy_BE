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

    /**
     * @OA\Post(
     *     path="/api/auth/{provider}/mobile",
     *     tags={"SocialAuth"},
     *     summary="Mobile social login",
     *     @OA\Parameter(name="provider", in="path", required=true, @OA\Schema(type="string")),
     *     @OA\Response(response=200, description="Login", @OA\JsonContent(ref="#/components/schemas/StandardSuccess"))
     * )
     */
    public function mobileLogin(string $provider)
    {
        return $this->socialAuthService->handleProviderCallback($provider);
    }
}
