<?php

namespace App\Http\Controllers\Tutors;

use App\Http\Controllers\Controller;
use App\Services\Tutors\FindTutorService;
use Illuminate\Http\Request;

class FindTutorController extends Controller
{
    public function __construct(private readonly FindTutorService $findTutorService)
    {
    }

    /**
     * @OA\Get(
     *     path="/api/find-tutor",
     *     tags={"Tutors"},
     *     summary="Find tutors",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(name="radius", in="query", required=false, @OA\Schema(type="integer", default=10)),
     *     @OA\Parameter(name="subject_id", in="query", required=false, @OA\Schema(type="integer")),
     *     @OA\Parameter(name="class_id", in="query", required=false, @OA\Schema(type="integer")),
     *     @OA\Parameter(name="min_rating", in="query", required=false, @OA\Schema(type="number")),
     *     @OA\Parameter(name="gender", in="query", required=false, @OA\Schema(type="string")),
     *     @OA\Parameter(name="weight_rating", in="query", required=false, @OA\Schema(type="number")),
     *     @OA\Parameter(name="weight_distance", in="query", required=false, @OA\Schema(type="number")),
     *     @OA\Parameter(name="page", in="query", required=false, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Tutors list", @OA\JsonContent(ref="#/components/schemas/StandardSuccess"))
     * )
     */
    public function search(Request $request)
    {
        $result = $this->findTutorService->search($request);

        return response()->json($result->payload, $result->code);
    }

    /**
     * @OA\Get(
     *     path="/api/find-tutor/{id}",
     *     tags={"Tutors"},
     *     summary="Find tutor detail",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Tutor detail", @OA\JsonContent(ref="#/components/schemas/StandardSuccess"))
     * )
     */
    public function show(Request $request, $id)
    {
        $result = $this->findTutorService->show($request, $id);

        return response()->json($result->payload, $result->code);
    }
}
