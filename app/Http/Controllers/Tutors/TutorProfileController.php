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

    public function show(Request $request, $id)
    {
        $result = $this->tutorProfileService->show($request, $id);

        return response()->json($result->payload, $result->code);
    }

    public function availableSlots(Request $request, $id)
    {
        $result = $this->tutorProfileService->availableSlots($request, $id);

        return response()->json($result->payload, $result->code);
    }
}
