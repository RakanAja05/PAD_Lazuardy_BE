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

    /**
     * @OA\Post(
     *     path="/api/register",
     *     tags={"Auth"},
     *     summary="Send register OTP",
    *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"email","password","password_confirmation"},
     *             @OA\Property(property="email", type="string"),
     *             @OA\Property(property="password", type="string"),
     *             @OA\Property(property="password_confirmation", type="string")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="OTP sent",
     *         @OA\JsonContent(
     *             allOf={
     *                 @OA\Schema(ref="#/components/schemas/StandardSuccess"),
     *                 @OA\Schema(
     *                     @OA\Property(property="data", type="object",
     *                         @OA\Property(property="otp", type="string")
     *                     )
     *                 )
     *             }
     *         )
     *     )
     * )
     */
    public function sendRegisterOtp(Request $request)
    {
        $result = $this->authControllerService->sendRegisterOtp($request);

        return response()->json($result->payload, $result->code);
    }

    /**
     * @OA\Patch(
     *     path="/api/register/verify",
     *     tags={"Auth"},
     *     summary="Verify register OTP",
    *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"email","otp"},
     *             @OA\Property(property="email", type="string"),
     *             @OA\Property(property="otp", type="string")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="OTP verified",
     *         @OA\JsonContent(ref="#/components/schemas/StandardSuccess")
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="OTP invalid",
     *         @OA\JsonContent(ref="#/components/schemas/StandardError")
     *     )
     * )
     */
    public function verifyRegisterOtp(Request $request)
    {
        $result = $this->authControllerService->verifyRegisterOtp($request);

        return response()->json($result->payload, $result->code);
    }

    /**
     * @OA\Patch(
     *     path="/api/register/resend-otp",
     *     tags={"Auth"},
     *     summary="Resend register OTP",
    *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"email"},
     *             @OA\Property(property="email", type="string")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="OTP resent",
     *         @OA\JsonContent(
     *             allOf={
     *                 @OA\Schema(ref="#/components/schemas/StandardSuccess"),
     *                 @OA\Schema(
     *                     @OA\Property(property="data", type="object",
     *                         @OA\Property(property="otp", type="string")
     *                     )
     *                 )
     *             }
     *         )
     *     )
     * )
     */
    public function resendRegisterOtp(Request $request)
    {
        $result = $this->authControllerService->resendRegisterOtp($request);

        return response()->json($result->payload, $result->code);
    }

    /**
     * @OA\Patch(
     *     path="/api/register/student",
     *     tags={"Auth"},
     *     summary="Register student",
    *     @OA\RequestBody(
     *         required=true,
    *         content={
    *             @OA\MediaType(
    *                 mediaType="application/json",
    *                 @OA\Schema(
    *                     required={"email","password","name","gender","date_of_birth","telephone_number","province","regency","district","subdistrict","street","latitude","longitude"},
    *                     @OA\Property(property="email", type="string"),
    *                     @OA\Property(property="password", type="string"),
    *                     @OA\Property(property="password_confirmation", type="string"),
    *                     @OA\Property(property="name", type="string"),
    *                     @OA\Property(property="gender", type="string", enum={"male","female"}),
    *                     @OA\Property(property="date_of_birth", type="string", example="2005-01-15"),
    *                     @OA\Property(property="telephone_number", type="string"),
    *                     @OA\Property(property="province", type="string"),
    *                     @OA\Property(property="regency", type="string"),
    *                     @OA\Property(property="district", type="string"),
    *                     @OA\Property(property="subdistrict", type="string"),
    *                     @OA\Property(property="street", type="string"),
    *                     @OA\Property(property="religion", type="string", enum={"islam","kristen","katolik","hindu","buddha","konghucu"}),
    *                     @OA\Property(property="latitude", type="number"),
    *                     @OA\Property(property="longitude", type="number"),
    *                     @OA\Property(property="class_id", type="integer"),
    *                     @OA\Property(property="curriculum_id", type="integer"),
    *                     @OA\Property(property="school", type="string"),
    *                     @OA\Property(property="parent", type="string"),
    *                     @OA\Property(property="parent_telephone_number", type="string")
    *                 )
    *             ),
    *             @OA\MediaType(
    *                 mediaType="multipart/form-data",
    *                 @OA\Schema(
    *                     required={"email","password","name","gender","date_of_birth","telephone_number","province","regency","district","subdistrict","street","latitude","longitude"},
    *                     @OA\Property(property="email", type="string"),
    *                     @OA\Property(property="password", type="string"),
    *                     @OA\Property(property="password_confirmation", type="string"),
    *                     @OA\Property(property="name", type="string"),
    *                     @OA\Property(property="gender", type="string", enum={"male","female"}),
    *                     @OA\Property(property="date_of_birth", type="string"),
    *                     @OA\Property(property="telephone_number", type="string"),
    *                     @OA\Property(property="profile_photo", type="string", format="binary"),
    *                     @OA\Property(property="province", type="string"),
    *                     @OA\Property(property="regency", type="string"),
    *                     @OA\Property(property="district", type="string"),
    *                     @OA\Property(property="subdistrict", type="string"),
    *                     @OA\Property(property="street", type="string"),
    *                     @OA\Property(property="religion", type="string", enum={"islam","kristen","katolik","hindu","buddha","konghucu"}),
    *                     @OA\Property(property="latitude", type="number"),
    *                     @OA\Property(property="longitude", type="number"),
    *                     @OA\Property(property="class_id", type="integer"),
    *                     @OA\Property(property="curriculum_id", type="integer"),
    *                     @OA\Property(property="school", type="string"),
    *                     @OA\Property(property="parent", type="string"),
    *                     @OA\Property(property="parent_telephone_number", type="string")
    *                 )
    *             )
    *         }
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Student registered",
     *         @OA\JsonContent(
     *             allOf={
     *                 @OA\Schema(ref="#/components/schemas/StandardSuccess"),
     *                 @OA\Schema(
     *                     @OA\Property(property="data", type="object",
     *                         @OA\Property(property="token", type="string")
     *                     )
     *                 )
     *             }
     *         )
     *     )
     * )
     */
    public function storeStudentRegister(StoreStudentRegisterRequest $request)
    {
        $result = $this->authControllerService->storeStudentRegister($request);

        return response()->json($result->payload, $result->code);
    }

    /**
     * @OA\Patch(
     *     path="/api/register/tutor",
     *     tags={"Auth"},
     *     summary="Register tutor",
    *     @OA\RequestBody(
     *         required=true,
     *         content={
     *             @OA\MediaType(
     *                 mediaType="application/json",
     *                 @OA\Schema(
     *                     required={"email","password","password_confirmation","name","gender","date_of_birth","telephone_number","province","regency","district","subdistrict","street","latitude","longitude","bank","rekening"},
     *                     @OA\Property(property="email", type="string"),
     *                     @OA\Property(property="password", type="string"),
     *                     @OA\Property(property="password_confirmation", type="string"),
     *                     @OA\Property(property="name", type="string"),
    *                     @OA\Property(property="gender", type="string", enum={"male","female"}),
     *                     @OA\Property(property="date_of_birth", type="string"),
     *                     @OA\Property(property="telephone_number", type="string"),
     *                     @OA\Property(property="province", type="string"),
     *                     @OA\Property(property="regency", type="string"),
     *                     @OA\Property(property="district", type="string"),
     *                     @OA\Property(property="subdistrict", type="string"),
     *                     @OA\Property(property="street", type="string"),
    *                     @OA\Property(property="religion", type="string", enum={"islam","kristen","katolik","hindu","buddha","konghucu"}),
     *                     @OA\Property(property="latitude", type="number"),
     *                     @OA\Property(property="longitude", type="number"),
     *                     @OA\Property(property="bank", type="string"),
     *                     @OA\Property(property="rekening", type="string")
     *                 )
     *             ),
     *             @OA\MediaType(
     *                 mediaType="multipart/form-data",
     *                 @OA\Schema(
     *                     required={"email","password","password_confirmation","name","gender","date_of_birth","telephone_number","province","regency","district","subdistrict","street","latitude","longitude","bank","rekening"},
     *                     @OA\Property(property="email", type="string"),
     *                     @OA\Property(property="password", type="string"),
     *                     @OA\Property(property="password_confirmation", type="string"),
     *                     @OA\Property(property="name", type="string"),
    *                     @OA\Property(property="gender", type="string", enum={"male","female"}),
     *                     @OA\Property(property="date_of_birth", type="string"),
     *                     @OA\Property(property="telephone_number", type="string"),
     *                     @OA\Property(property="profile_photo", type="string", format="binary"),
     *                     @OA\Property(property="province", type="string"),
     *                     @OA\Property(property="regency", type="string"),
     *                     @OA\Property(property="district", type="string"),
     *                     @OA\Property(property="subdistrict", type="string"),
     *                     @OA\Property(property="street", type="string"),
    *                     @OA\Property(property="religion", type="string", enum={"islam","kristen","katolik","hindu","buddha","konghucu"}),
     *                     @OA\Property(property="latitude", type="number"),
     *                     @OA\Property(property="longitude", type="number"),
     *                     @OA\Property(property="bank", type="string"),
     *                     @OA\Property(property="rekening", type="string")
     *                 )
     *             )
     *         }
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Tutor registered",
     *         @OA\JsonContent(
     *             allOf={
     *                 @OA\Schema(ref="#/components/schemas/StandardSuccess"),
     *                 @OA\Schema(
     *                     @OA\Property(property="data", type="object",
     *                         @OA\Property(property="token", type="string")
     *                     )
     *                 )
     *             }
     *         )
     *     )
     * )
     */
    public function storeTutorRegister(StoreTutorRegisterRequest $request)
    {
        $result = $this->authControllerService->storeTutorRegister($request);

        return response()->json($result->payload, $result->code);
    }

    /**
     * @OA\Post(
     *     path="/api/forgot-password",
     *     tags={"Auth"},
     *     summary="Request forgot password OTP",
    *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"email"},
     *             @OA\Property(property="email", type="string")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="OTP sent",
     *         @OA\JsonContent(
     *             allOf={
     *                 @OA\Schema(ref="#/components/schemas/StandardSuccess"),
     *                 @OA\Schema(
     *                     @OA\Property(property="data", type="object",
     *                         @OA\Property(property="otp", type="string"),
     *                         @OA\Property(property="temp_token", type="string")
     *                     )
     *                 )
     *             }
     *         )
     *     )
     * )
     */
    public function forgotPassword(Request $request)
    {
        $result = $this->authControllerService->forgotPassword($request);

        return response()->json($result->payload, $result->code);
    }

    /**
     * @OA\Patch(
     *     path="/api/forgot-password/verify",
     *     tags={"Auth"},
     *     summary="Verify forgot password OTP",
    *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"otp_code","temp_token"},
     *             @OA\Property(property="otp_code", type="string"),
     *             @OA\Property(property="temp_token", type="string")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="OTP verified",
     *         @OA\JsonContent(
     *             allOf={
     *                 @OA\Schema(ref="#/components/schemas/StandardSuccess"),
     *                 @OA\Schema(
     *                     @OA\Property(property="data", type="object",
     *                         @OA\Property(property="token", type="string")
     *                     )
     *                 )
     *             }
     *         )
     *     )
     * )
     */
    public function verifyForgotPassword(VerifyOtpRequest $request)
    {
        $result = $this->authControllerService->verifyForgotPassword($request);

        return response()->json($result->payload, $result->code);
    }

    /**
     * @OA\Patch(
     *     path="/api/reset-password",
     *     tags={"Auth"},
     *     summary="Reset password",
    *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"token","password","password_confirmation"},
     *             @OA\Property(property="token", type="string"),
     *             @OA\Property(property="password", type="string"),
     *             @OA\Property(property="password_confirmation", type="string")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Password reset",
     *         @OA\JsonContent(ref="#/components/schemas/StandardSuccess")
     *     )
     * )
     */
    public function resetPassword(UpdateAuthRequest $request)
    {
        $result = $this->authControllerService->resetPassword($request);

        return response()->json($result->payload, $result->code);
    }
}
