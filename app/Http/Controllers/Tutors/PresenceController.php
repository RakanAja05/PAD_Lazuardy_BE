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

    public function index(Request $request)
    {
        $result = $this->presenceService->index($request);

        return response()->json($result->payload, $result->code);
    }

    public function store(Request $request)
    {
        $result = $this->presenceService->store($request);

        return response()->json($result->payload, $result->code);
    }
}
