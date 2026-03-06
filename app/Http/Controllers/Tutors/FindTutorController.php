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

    public function search(Request $request)
    {
        $result = $this->findTutorService->search($request);

        return response()->json($result->payload, $result->code);
    }

    public function show(Request $request, $id)
    {
        $result = $this->findTutorService->show($request, $id);

        return response()->json($result->payload, $result->code);
    }
}
