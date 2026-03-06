<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\Admin\TutorVerifyService;
use Illuminate\Http\Request;

class TutorVerifyController extends Controller
{
    public function __construct(private readonly TutorVerifyService $tutorVerifyService)
    {
    }

    /**
     * @OA\Get(
     *     path="/api/verify/tutor",
     *     tags={"Admin"},
     *     summary="List tutors pending verification",
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(response=200, description="Pending tutors", @OA\JsonContent(ref="#/components/schemas/StandardSuccess"))
     * )
     */
    public function index()
    {
        $result = $this->tutorVerifyService->index();

        return response()->json($result->payload, $result->code);
    }

    /**
     * @OA\Patch(
     *     path="/api/verify/tutor/approve",
     *     tags={"Admin"},
     *     summary="Approve tutor verification",
     *     security={{"bearerAuth":{}}},
    *     @OA\RequestBody(
    *         required=true,
    *         @OA\JsonContent(
    *             required={"user_id"},
    *             @OA\Property(property="user_id", type="integer")
    *         )
    *     ),
     *     @OA\Response(response=200, description="Approved", @OA\JsonContent(ref="#/components/schemas/StandardSuccess"))
     * )
     */
    public function approve(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:tutors,user_id',
        ]);

        $result = $this->tutorVerifyService->approve($validated);

        return response()->json($result->payload, $result->code);
    }

    /**
     * @OA\Patch(
     *     path="/api/verify/tutor/reject",
     *     tags={"Admin"},
     *     summary="Reject tutor verification",
     *     security={{"bearerAuth":{}}},
    *     @OA\RequestBody(
    *         required=true,
    *         @OA\JsonContent(
    *             required={"user_id"},
    *             @OA\Property(property="user_id", type="integer"),
    *             @OA\Property(property="reason", type="string", nullable=true)
    *         )
    *     ),
     *     @OA\Response(response=200, description="Rejected", @OA\JsonContent(ref="#/components/schemas/StandardSuccess"))
     * )
     */
    public function reject(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:tutors,user_id',
            'reason' => 'nullable|string|max:500',
        ]);

        $result = $this->tutorVerifyService->reject($validated);

        return response()->json($result->payload, $result->code);
    }
}
