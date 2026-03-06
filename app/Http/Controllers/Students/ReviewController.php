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

    public function index(Request $request)
    {
        $result = $this->reviewService->index($request);

        return response()->json($result->payload, $result->code);
    }

    public function storeOrUpdate(Request $request)
    {
        $result = $this->reviewService->storeOrUpdate($request);

        return response()->json($result->payload, $result->code);
    }

    public function show(Request $request)
    {
        $result = $this->reviewService->show($request);

        return response()->json($result->payload, $result->code);
    }
}
