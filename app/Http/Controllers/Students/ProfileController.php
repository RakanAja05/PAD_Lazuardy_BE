<?php

namespace App\Http\Controllers\Students;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateStudentProfileRequest;
use App\Http\Requests\UpdateTutorLessonMethodRequest;
use App\Http\Requests\UpdateTutorProfileRequest;
use App\Services\Students\ProfileService;
use Illuminate\Http\Request;

class ProfileController extends Controller
{
    public function __construct(private readonly ProfileService $profileService)
    {
    }

    public function showStudentProfile(Request $request)
    {
        $result = $this->profileService->showStudentProfile($request);

        return response()->json($result->payload, $result->code);
    }

    public function showTutorProfile(Request $request)
    {
        $result = $this->profileService->showTutorProfile($request);

        return response()->json($result->payload, $result->code);
    }

    public function updateStudentProfile(UpdateStudentProfileRequest $request)
    {
        $result = $this->profileService->updateStudentProfile($request);

        return response()->json($result->payload, $result->code);
    }

    public function updateTutorProfile(UpdateTutorProfileRequest $request)
    {
        $result = $this->profileService->updateTutorProfile($request);

        return response()->json($result->payload, $result->code);
    }

    public function showTutorLessonMethod(Request $request)
    {
        $result = $this->profileService->showTutorLessonMethod($request);

        return response()->json($result->payload, $result->code);
    }

    public function updateTutorLessonMethod(UpdateTutorLessonMethodRequest $request)
    {
        $result = $this->profileService->updateTutorLessonMethod($request);

        return response()->json($result->payload, $result->code);
    }
}
