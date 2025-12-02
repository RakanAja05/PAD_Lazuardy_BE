<?php

namespace App\Http\Controllers;

use App\Enums\RatingOptionEnum;
use App\Enums\RoleEnum;
use App\Models\Review;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Validation\Rules\Enum;

class ReviewController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/student/review",
     *     tags={"Reviews"},
     *     summary="Get list of tutors to review",
     *     description="Menampilkan daftar tutor yang pernah mengajar student untuk diberi review.",
     *     security={{"sanctum":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Success",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="data", type="array",
     *                 @OA\Items(
     *                     @OA\Property(property="tutor_id", type="integer"),
     *                     @OA\Property(property="tutor_name", type="string"),
     *                     @OA\Property(property="tutor_classes", type="array", @OA\Items(type="string")),
     *                     @OA\Property(property="purchased_subject_name", type="string")
     *                 )
     *             )
     *         )
     *     )
     * )
     */
    public function index(Request $request)
    {
        $user = $request->user()->load([
            'studentPackageStudents.tutor.subjects.class',
            'studentPackageStudents.subject' 
        ]);

        $tutorsToReview = $user->studentPackageStudents
            ->unique('tutor_user_id')
            ->map(function ($studentPackageStudent) {
                $tutor = $studentPackageStudent->tutor;

                $classNames = $tutor->subjects
                    ->pluck('class.name')
                    ->unique()
                    ->toArray();

                return [
                    'tutor_id' => $tutor->id,
                    'tutor_name' => $tutor->name,
                    'tutor_description' => $tutor->description,
                    'tutor_classes' => $classNames, 
                    'purchased_subject_name' => $studentPackageStudent->subject->name, 
                ];
            });

        return response()->json([
            'status' => 'success',
            'message' => 'Data daftar tutor berhasil terkirim',
            'data' => $tutorsToReview
        ], 200);
    }

    /**
     * @OA\Post(
     *     path="/api/student/review",
     *     tags={"Reviews"},
     *     summary="Create or update review for tutor",
     *     description="Student memberikan review dan rating untuk tutor. Jika sudah pernah review, akan di-update.",
     *     security={{"sanctum":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"tutor_id", "quality", "delivery", "attitude", "benefit", "rate"},
     *             @OA\Property(property="tutor_id", type="integer", example=2),
     *             @OA\Property(property="quality", type="string", enum={"poor", "average", "good", "excellent"}),
     *             @OA\Property(property="delivery", type="string", enum={"poor", "average", "good", "excellent"}),
     *             @OA\Property(property="attitude", type="string", enum={"poor", "average", "good", "excellent"}),
     *             @OA\Property(property="benefit", type="string", enum={"poor", "average", "good", "excellent"}),
     *             @OA\Property(property="rate", type="integer", example=5, description="Rating 1-5"),
     *             @OA\Property(property="review", type="string", example="Tutor sangat baik dan sabar")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Review created/updated",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string")
     *         )
     *     )
     * )
     */
    public function storeOrUpdate(Request $request)
    {
        $request->validate([
            'tutor_id' => ['required', 'integer', 'exists:users,id, role,tutor'],
            'quality' => ['required', new Enum(RatingOptionEnum::class)],
            'delivery' => ['required', new Enum(RatingOptionEnum::class)],
            'attitude' => ['required', new Enum(RatingOptionEnum::class)],
            'benefit' => ['required', new Enum(RatingOptionEnum::class)],
            'rate' => ['required', 'integer'],
            'review' => ['nullable', 'string'],
        ]);
        
        $tutor = User::where('id', $request->tutor_id)
        ->where('role', RoleEnum::TUTOR)
        ->firstOrFail();

        $student = $request->user();
        
        $data = $request->only(['quality', 'delivery', 'attitude', 'benefit', 'rate', 'review']);
        try {
            Review::updateOrCreate([
                'from_user_id' => $student->id,
                'to_user_id' => $tutor->id,
            ], $data);

            return response()->json([
                'status' => 'success',
                'message' => 'Review berhasil terkirim'
            ],201);

        } catch(Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Review gagal terkirim: ' . $e->getMessage(),
            ], 401);
        }
    }

    public function show(Request $request){
        $request->validate([
            'tutor_id' => ['required', 'integer', 'exists:users,id, role,tutor'],
        ]);
        $student = $request->user();

        $reviewData = Review::where('from_user_id', $student->id)
            ->where('to_user_id', $request->tutor_id)
            ->first();
        
        return response()->json([
            'status' => 'success',
            'data' => $reviewData,
        ], 200);
    }
}
