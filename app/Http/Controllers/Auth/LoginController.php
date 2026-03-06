<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Services\Auth\LoginService;
use Illuminate\Http\Request;

class LoginController extends Controller
{
    public function __construct(private readonly LoginService $loginService)
    {
    }

    public function login(Request $request)
    {
        $result = $this->loginService->login($request);

        return response()->json($result->payload, $result->code);
    }

    public function logout(Request $request)
    {
        $result = $this->loginService->logout($request);

        return response()->json($result->payload, $result->code);
    }

    public function me(Request $request)
    {
        $result = $this->loginService->me($request);

        return response()->json($result->payload, $result->code);
    }
}
