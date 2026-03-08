<?php

namespace App\Http\Controllers\Students;

use App\Http\Controllers\Controller;
use App\Services\Students\ReviewService;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    public function __construct(private readonly ReviewService $reviewService)
    {
    }

    /**
     * @OA\Get(
     *     path="/api/student/review",
     *     tags={"Students"},
     *     summary="List tutors to review",
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(response=200, description="Review list", @OA\JsonContent(ref="#/components/schemas/StandardSuccess"))
     * )
     */
    public function index(Request $request)
    {
        $result = $this->reviewService->index($request);

        return response()->json($result->payload, $result->code);
    }

    /**
     * @OA\Post(
     *     path="/api/student/review",
     *     tags={"Students"},
     *     summary="Create review",
     *     security={{"bearerAuth":{}}},
    *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"tutor_id","quality","delivery","attitude","benefit","rate"},
     *             @OA\Property(property="tutor_id", type="integer"),
    *             @OA\Property(property="quality", type="string", enum={"sangat baik","baik","cukup","buruk","sangat buruk"}),
    *             @OA\Property(property="delivery", type="string", enum={"sangat baik","baik","cukup","buruk","sangat buruk"}),
    *             @OA\Property(property="attitude", type="string", enum={"sangat baik","baik","cukup","buruk","sangat buruk"}),
    *             @OA\Property(property="benefit", type="string", enum={"sangat baik","baik","cukup","buruk","sangat buruk"}),
     *             @OA\Property(property="rate", type="integer"),
     *             @OA\Property(property="review", type="string")
     *         )
     *     ),
     *     @OA\Response(response=201, description="Review saved", @OA\JsonContent(ref="#/components/schemas/StandardSuccess"))
     * )
     */
    /**
     * @OA\Patch(
     *     path="/api/student/review",
     *     tags={"Students"},
     *     summary="Update review",
     *     security={{"bearerAuth":{}}},
    *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"tutor_id","quality","delivery","attitude","benefit","rate"},
     *             @OA\Property(property="tutor_id", type="integer"),
    *             @OA\Property(property="quality", type="string", enum={"sangat baik","baik","cukup","buruk","sangat buruk"}),
    *             @OA\Property(property="delivery", type="string", enum={"sangat baik","baik","cukup","buruk","sangat buruk"}),
    *             @OA\Property(property="attitude", type="string", enum={"sangat baik","baik","cukup","buruk","sangat buruk"}),
    *             @OA\Property(property="benefit", type="string", enum={"sangat baik","baik","cukup","buruk","sangat buruk"}),
     *             @OA\Property(property="rate", type="integer"),
     *             @OA\Property(property="review", type="string")
     *         )
     *     ),
     *     @OA\Response(response=201, description="Review saved", @OA\JsonContent(ref="#/components/schemas/StandardSuccess"))
     * )
     */
    public function storeOrUpdate(Request $request)
    {
        $result = $this->reviewService->storeOrUpdate($request);

        return response()->json($result->payload, $result->code);
    }

    /**
     * @OA\Get(
     *     path="/api/student/review/{tutor_id}",
     *     tags={"Students"},
     *     summary="Get review detail",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(name="tutor_id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Review detail", @OA\JsonContent(ref="#/components/schemas/StandardSuccess"))
     * )
     */
    public function show(Request $request)
    {
        $result = $this->reviewService->show($request);

        return response()->json($result->payload, $result->code);
    }
}
