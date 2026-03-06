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

    public function packages(Request $request)
    {
        $result = $this->studyPackageService->packages($request);

        return response()->json($result->payload, $result->code);
    }

    public function index(Request $request)
    {
        $result = $this->studyPackageService->index($request);

        return response()->json($result->payload, $result->code);
    }

    public function show(Request $request, $packageId)
    {
        $result = $this->studyPackageService->show($request, $packageId);

        return response()->json($result->payload, $result->code);
    }
}
