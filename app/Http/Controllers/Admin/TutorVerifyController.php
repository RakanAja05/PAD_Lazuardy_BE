<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\Admin\TutorVerifyService;
use Illuminate\Http\Request;

class TutorVerifyController extends Controller
{
    public function __construct(private readonly TutorVerifyService $tutorVerifyService)
    {
    }

    public function index()
    {
        $result = $this->tutorVerifyService->index();

        return response()->json($result->payload, $result->code);
    }

    public function approve(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:tutors,user_id',
        ]);

        $result = $this->tutorVerifyService->approve($validated);

        return response()->json($result->payload, $result->code);
    }

    public function reject(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:tutors,user_id',
            'reason' => 'nullable|string|max:500',
        ]);

        $result = $this->tutorVerifyService->reject($validated);

        return response()->json($result->payload, $result->code);
    }
}
