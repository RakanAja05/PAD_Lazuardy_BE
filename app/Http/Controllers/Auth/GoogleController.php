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

    /**
     * @OA\Post(
     *     path="/api/auth/google/complete",
     *     tags={"Auth"},
     *     summary="Complete Google registration",
    *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"google_id","name","email","role"},
     *             @OA\Property(property="google_id", type="string"),
     *             @OA\Property(property="name", type="string"),
     *             @OA\Property(property="email", type="string"),
     *             @OA\Property(property="role", type="string", example="student"),
     *             @OA\Property(property="telephone_number", type="string"),
     *             @OA\Property(property="date_of_birth", type="string"),
     *             @OA\Property(property="gender", type="string"),
     *             @OA\Property(property="profile_photo_url", type="string")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Google registration completed",
     *         @OA\JsonContent(
     *             allOf={
     *                 @OA\Schema(ref="#/components/schemas/StandardSuccess"),
     *                 @OA\Schema(
     *                     @OA\Property(property="data", type="object",
     *                         @OA\Property(property="user", ref="#/components/schemas/UserPublic"),
     *                         @OA\Property(property="token", type="string")
     *                     )
     *                 )
     *             }
     *         )
     *     )
     * )
     */
    public function completeGoogleRegistration(Request $request)
    {
        $result = $this->googleAuthService->completeGoogleRegistration($request);

        return response()->json($result->payload, $result->code);
    }
}
