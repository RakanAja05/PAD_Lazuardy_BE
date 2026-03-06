<?php

namespace App\Http\Controllers\Tutors;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreTutorApplicationRequest;
use App\Services\Tutors\TutorApplicationService;

class TutorApplicationController extends Controller
{
    public function __construct(private readonly TutorApplicationService $tutorApplicationService)
    {
    }

    public function store(StoreTutorApplicationRequest $request)
    {
        $result = $this->tutorApplicationService->store($request);

        return response()->json($result->payload, $result->code);
    }
}
