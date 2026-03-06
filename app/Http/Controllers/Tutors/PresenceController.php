<?php

namespace App\Http\Controllers\Tutors;

use App\Http\Controllers\Controller;
use App\Services\Tutors\PresenceService;
use Illuminate\Http\Request;

class PresenceController extends Controller
{
    public function __construct(private readonly PresenceService $presenceService)
    {
    }

    /**
     * @OA\Get(
     *     path="/api/tutor/presence",
     *     tags={"Tutors"},
     *     summary="Get tutor presence list",
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(response=200, description="Presence list", @OA\JsonContent(ref="#/components/schemas/StandardSuccess"))
     * )
     */
    public function index(Request $request)
    {
        $result = $this->presenceService->index($request);

        return response()->json($result->payload, $result->code);
    }

    /**
     * @OA\Post(
     *     path="/api/tutor/presence",
     *     tags={"Tutors"},
     *     summary="Create presence",
     *     security={{"bearerAuth":{}}},
    *     @OA\RequestBody(
     *         required=true,
     *         content={
    *             @OA\MediaType(
    *                 mediaType="multipart/form-data",
    *                 @OA\Schema(
    *                     required={"taken_schedule_id","student_user_id","material","evaluation","grade","photo"},
    *                     @OA\Property(property="taken_schedule_id", type="integer"),
    *                     @OA\Property(property="student_user_id", type="integer"),
    *                     @OA\Property(property="material", type="string"),
    *                     @OA\Property(property="evaluation", type="string"),
    *                     @OA\Property(property="grade", type="integer"),
    *                     @OA\Property(property="photo", type="string", format="binary")
    *                 )
    *             )
     *         }
     *     ),
     *     @OA\Response(response=201, description="Presence created", @OA\JsonContent(ref="#/components/schemas/StandardSuccess"))
     * )
     */
    public function store(Request $request)
    {
        $result = $this->presenceService->store($request);

        return response()->json($result->payload, $result->code);
    }
}
