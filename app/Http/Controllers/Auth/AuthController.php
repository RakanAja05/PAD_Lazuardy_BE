<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreStudentRegisterRequest;
use App\Http\Requests\StoreTutorRegisterRequest;
use App\Http\Requests\UpdateAuthRequest;
use App\Http\Requests\VerifyOtpRequest;
use App\Services\Auth\AuthControllerService;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function __construct(private readonly AuthControllerService $authControllerService)
    {
    }

    public function sendRegisterOtp(Request $request)
    {
        $result = $this->authControllerService->sendRegisterOtp($request);

        return response()->json($result->payload, $result->code);
    }

    public function verifyRegisterOtp(Request $request)
    {
        $result = $this->authControllerService->verifyRegisterOtp($request);

        return response()->json($result->payload, $result->code);
    }

    public function resendRegisterOtp(Request $request)
    {
        $result = $this->authControllerService->resendRegisterOtp($request);

        return response()->json($result->payload, $result->code);
    }

    public function storeStudentRegister(StoreStudentRegisterRequest $request)
    {
        $result = $this->authControllerService->storeStudentRegister($request);

        return response()->json($result->payload, $result->code);
    }

    public function storeTutorRegister(StoreTutorRegisterRequest $request)
    {
        $result = $this->authControllerService->storeTutorRegister($request);

        return response()->json($result->payload, $result->code);
    }

    public function forgotPassword(Request $request)
    {
        $result = $this->authControllerService->forgotPassword($request);

        return response()->json($result->payload, $result->code);
    }

    public function verifyForgotPassword(VerifyOtpRequest $request)
    {
        $result = $this->authControllerService->verifyForgotPassword($request);

        return response()->json($result->payload, $result->code);
    }

    public function resetPassword(UpdateAuthRequest $request)
    {
        $result = $this->authControllerService->resetPassword($request);

        return response()->json($result->payload, $result->code);
    }
}
