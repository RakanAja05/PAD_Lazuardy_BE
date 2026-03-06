<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Services\Auth\GoogleAuthService;
use Illuminate\Http\Request;

class GoogleController extends Controller
{
    public function __construct(private readonly GoogleAuthService $googleAuthService)
    {
    }

    public function redirectToGoogle()
    {
        $result = $this->googleAuthService->redirectToGoogle();

        return $result->payload;
    }

    public function handleGoogleCallback()
    {
        $result = $this->googleAuthService->handleGoogleCallback();

        return response()->json($result->payload, $result->code);
    }

    public function completeGoogleRegistration(Request $request)
    {
        $result = $this->googleAuthService->completeGoogleRegistration($request);

        return response()->json($result->payload, $result->code);
    }
}
