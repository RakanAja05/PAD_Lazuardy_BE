<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreUserRequest;
use App\Services\AuthService;

class AuthController extends Controller
{
    public function register(StoreUserRequest $request, AuthService $authService)
    {
        $validatedData = $request->validated();

        $result = $authService->registerUser($validatedData);

        return response()->json([
            'message' => 'Register successful',
            'access_token' => $result['token'],
            'token_type' => 'Bearer',
        ], 201);
    }
}
