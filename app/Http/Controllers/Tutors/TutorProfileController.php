<?php

namespace App\Http\Controllers\Tutors;

use App\Http\Controllers\Controller;
use App\Services\Tutors\TutorProfileService;
use Illuminate\Http\Request;

class TutorProfileController extends Controller
{
    public function __construct(private readonly TutorProfileService $tutorProfileService)
    {
    }

    /**
     * @OA\Get(
     *     path="/api/tutor-profile/{id}",
     *     tags={"Tutors"},
     *     summary="Tutor profile",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Tutor profile", @OA\JsonContent(ref="#/components/schemas/StandardSuccess"))
     * )
     */
    public function show(Request $request, $id)
    {
        $result = $this->tutorProfileService->show($request, $id);

        return response()->json($result->payload, $result->code);
    }

    /**
     * @OA\Get(
     *     path="/api/tutor-profile/{id}/available-slots",
     *     tags={"Tutors"},
     *     summary="Tutor available slots",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Parameter(name="date", in="query", required=true, @OA\Schema(type="string", example="2026-03-06")),
     *     @OA\Response(response=200, description="Available slots", @OA\JsonContent(ref="#/components/schemas/StandardSuccess"))
     * )
     */
    public function availableSlots(Request $request, $id)
    {
        $result = $this->tutorProfileService->availableSlots($request, $id);

        return response()->json($result->payload, $result->code);
    }
}
