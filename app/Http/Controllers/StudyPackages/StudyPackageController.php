<?php

namespace App\Http\Controllers\StudyPackages;

use App\Http\Controllers\Controller;
use App\Services\StudyPackages\StudyPackageService;
use Illuminate\Http\Request;

class StudyPackageController extends Controller
{
    public function __construct(private readonly StudyPackageService $studyPackageService)
    {
    }

    /**
     * @OA\Get(
     *     path="/api/my-packages",
     *     tags={"StudyPackages"},
     *     summary="Get student's purchased packages",
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(response=200, description="Packages", @OA\JsonContent(ref="#/components/schemas/StandardSuccess"))
     * )
     */
    public function packages(Request $request)
    {
        $result = $this->studyPackageService->packages($request);

        return response()->json($result->payload, $result->code);
    }

    /**
     * @OA\Get(
     *     path="/api/study-packages",
     *     tags={"StudyPackages"},
     *     summary="Get study packages",
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(response=200, description="Packages", @OA\JsonContent(ref="#/components/schemas/StandardSuccess"))
     * )
     */
    public function index(Request $request)
    {
        $result = $this->studyPackageService->index($request);

        return response()->json($result->payload, $result->code);
    }

    /**
     * @OA\Get(
     *     path="/api/study-packages/{id}",
     *     tags={"StudyPackages"},
     *     summary="Get study package detail",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Package detail", @OA\JsonContent(ref="#/components/schemas/StandardSuccess"))
     * )
     */
    public function show(Request $request, $packageId)
    {
        $result = $this->studyPackageService->show($request, $packageId);

        return response()->json($result->payload, $result->code);
    }
}
