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

    /**
     * @OA\Get(
     *     path="/api/student/profile",
     *     tags={"Students"},
     *     summary="Student profile",
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(response=200, description="Profile", @OA\JsonContent(ref="#/components/schemas/StandardSuccess"))
     * )
     */
    /**
     * @OA\Get(
     *     path="/api/student/profile/edit",
     *     tags={"Students"},
     *     summary="Student profile (edit)",
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(response=200, description="Profile", @OA\JsonContent(ref="#/components/schemas/StandardSuccess"))
     * )
     */
    public function showStudentProfile(Request $request)
    {
        $result = $this->profileService->showStudentProfile($request);

        return response()->json($result->payload, $result->code);
    }

    /**
     * @OA\Get(
     *     path="/api/tutor/profile",
     *     tags={"Tutors"},
     *     summary="Get tutor profile",
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(response=200, description="Tutor profile", @OA\JsonContent(ref="#/components/schemas/StandardSuccess"))
     * )
     */
    /**
     * @OA\Get(
     *     path="/api/tutor/profile/edit",
     *     tags={"Tutors"},
     *     summary="Get tutor profile (edit)",
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(response=200, description="Tutor profile", @OA\JsonContent(ref="#/components/schemas/StandardSuccess"))
     * )
     */
    public function showTutorProfile(Request $request)
    {
        $result = $this->profileService->showTutorProfile($request);

        return response()->json($result->payload, $result->code);
    }

    /**
     * @OA\Patch(
     *     path="/api/student/profile",
     *     tags={"Students"},
     *     summary="Update student profile",
     *     security={{"bearerAuth":{}}},
    *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name","telephone_number","profile_photo_url","gender","date_of_birth","religion","province","regency","district","subdistrict","street","school"},
     *             @OA\Property(property="name", type="string"),
     *             @OA\Property(property="telephone_number", type="string"),
     *             @OA\Property(property="profile_photo_url", type="string"),
    *             @OA\Property(property="gender", type="string", enum={"male","female"}),
     *             @OA\Property(property="date_of_birth", type="string"),
    *             @OA\Property(property="religion", type="string", enum={"islam","kristen","katolik","hindu","buddha","konghucu"}),
     *             @OA\Property(property="province", type="string"),
     *             @OA\Property(property="regency", type="string"),
     *             @OA\Property(property="district", type="string"),
     *             @OA\Property(property="subdistrict", type="string"),
     *             @OA\Property(property="street", type="string"),
     *             @OA\Property(property="school", type="string"),
     *             @OA\Property(property="class_id", type="integer"),
     *             @OA\Property(property="curriculum_id", type="integer"),
     *             @OA\Property(property="parent", type="string"),
     *             @OA\Property(property="parent_telephone_number", type="string")
     *         )
     *     ),
     *     @OA\Response(response=200, description="Profile updated", @OA\JsonContent(ref="#/components/schemas/StandardSuccess"))
     * )
     */
    public function updateStudentProfile(UpdateStudentProfileRequest $request)
    {
        $result = $this->profileService->updateStudentProfile($request);

        return response()->json($result->payload, $result->code);
    }

    /**
     * @OA\Patch(
     *     path="/api/tutor/profile",
     *     tags={"Tutors"},
     *     summary="Update tutor profile",
     *     security={{"bearerAuth":{}}},
    *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name","gender","date_of_birth","religion","telephone_number","province","regency","district","subdistrict","street"},
     *             @OA\Property(property="name", type="string"),
    *             @OA\Property(property="gender", type="string", enum={"male","female"}),
     *             @OA\Property(property="date_of_birth", type="string"),
    *             @OA\Property(property="religion", type="string", enum={"islam","kristen","katolik","hindu","buddha","konghucu"}),
     *             @OA\Property(property="telephone_number", type="string"),
     *             @OA\Property(property="province", type="string"),
     *             @OA\Property(property="regency", type="string"),
     *             @OA\Property(property="district", type="string"),
     *             @OA\Property(property="subdistrict", type="string"),
     *             @OA\Property(property="street", type="string"),
     *             @OA\Property(property="bank", type="string"),
     *             @OA\Property(property="rekening", type="string")
     *         )
     *     ),
     *     @OA\Response(response=200, description="Profile updated", @OA\JsonContent(ref="#/components/schemas/StandardSuccess"))
     * )
     */
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

    /**
     * @OA\Patch(
     *     path="/api/tutor/lesson-formulir",
     *     tags={"Tutors"},
     *     summary="Update tutor lesson method",
     *     security={{"bearerAuth":{}}},
    *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"course_mode","description","qualification","learning_method","schedules"},
    *             @OA\Property(property="course_mode", type="string", enum={"online","offline"}),
     *             @OA\Property(property="description", type="string"),
     *             @OA\Property(property="qualification", type="array", @OA\Items(type="string")),
     *             @OA\Property(property="learning_method", type="string"),
     *             @OA\Property(property="schedules", type="array", @OA\Items(type="object",
    *                 @OA\Property(property="day", type="string", enum={"minggu","senin","selasa","rabu","kamis","jumat","sabtu"}),
     *                 @OA\Property(property="time", type="string", example="08:00")
     *             ))
     *         )
     *     ),
     *     @OA\Response(response=200, description="Lesson method updated", @OA\JsonContent(ref="#/components/schemas/StandardSuccess"))
     * )
     */
    public function updateTutorLessonMethod(UpdateTutorLessonMethodRequest $request)
    {
        $result = $this->profileService->updateTutorLessonMethod($request);

        return response()->json($result->payload, $result->code);
    }
}
