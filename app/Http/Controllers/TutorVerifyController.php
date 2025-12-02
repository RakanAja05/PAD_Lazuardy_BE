<?php

namespace App\Http\Controllers;

use App\Enums\TutorStatusEnum;
use App\Models\Tutor;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Exception;

class TutorVerifyController extends Controller
{
    /**
     * Return paginated list of tutors that need verification (status = verify).
     * Only returns fields needed by admin for verification.
     * 
     * @OA\Get(
     *     path="/api/verify/tutor",
     *     tags={"Admin - Tutor Verification"},
     *     summary="Get list of tutors pending verification",
     *     description="Menampilkan daftar tutor yang perlu diverifikasi oleh admin (status = verify). Termasuk data user, subjects, dan files yang diupload.",
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="page",
     *         in="query",
     *         description="Page number for pagination",
     *         required=false,
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Success",
     *         @OA\JsonContent(
     *             @OA\Property(property="tutors", type="object",
     *                 @OA\Property(property="data", type="array",
     *                     @OA\Items(
     *                         @OA\Property(property="user", type="object",
     *                             @OA\Property(property="id", type="integer", example=2),
     *                             @OA\Property(property="name", type="string", example="Budi Santoso"),
     *                             @OA\Property(property="email", type="string", example="budi@example.com")
     *                         ),
     *                         @OA\Property(property="price", type="integer", example=150000),
     *                         @OA\Property(property="badge", type="string", example="bronze"),
     *                         @OA\Property(property="course_mode", type="string", example="online"),
     *                         @OA\Property(property="description", type="string", example="Pengalaman mengajar 5 tahun"),
     *                         @OA\Property(property="education", type="array", @OA\Items(type="object")),
     *                         @OA\Property(property="qualification", type="array", @OA\Items(type="string")),
     *                         @OA\Property(property="experience", type="string"),
     *                         @OA\Property(property="organization", type="array", @OA\Items(type="object")),
     *                         @OA\Property(property="learning_method", type="string"),
     *                         @OA\Property(property="subjects", type="array",
     *                             @OA\Items(
     *                                 @OA\Property(property="id", type="integer", example=1),
     *                                 @OA\Property(property="name", type="string", example="Matematika SD")
     *                             )
     *                         ),
     *                         @OA\Property(property="files", type="array",
     *                             @OA\Items(
     *                                 @OA\Property(property="id", type="integer", example=1),
     *                                 @OA\Property(property="name", type="string", example="ijazah.pdf"),
     *                                 @OA\Property(property="type", type="string", example="certificate"),
     *                                 @OA\Property(property="path_url", type="string", example="uploads/files/ijazah.pdf")
     *                             )
     *                         )
     *                     )
     *                 ),
     *                 @OA\Property(property="current_page", type="integer", example=1),
     *                 @OA\Property(property="per_page", type="integer", example=9),
     *                 @OA\Property(property="total", type="integer", example=15)
     *             )
     *         )
     *     ),
     *     @OA\Response(response=401, description="Unauthorized - Token invalid atau tidak ada"),
     *     @OA\Response(response=403, description="Forbidden - Bukan role admin")
     * )
     */
    public function index(): JsonResponse
    {
        $tutorsPaginator = Tutor::with(['user.files', 'subjects'])
            ->where('status', TutorStatusEnum::VERIFY->value)
            ->paginate(9);

        $tutorsCollection = $tutorsPaginator->getCollection()->map(function ($t) {
            return [
                'user' => $t->user ? [
                    'id' => $t->user->id,
                    'name' => $t->user->name,
                    'email' => $t->user->email,
                ] : null,
                'price' => $t->price,
                'badge' => $t->badge?->value ?? null,
                'course_mode' => $t->course_mode?->value ?? null,
                'description' => $t->description,
                'education' => $t->education,
                'qualification' => $t->qualification,
                'experience' => $t->experience,
                'organization' => $t->organization,
                'learning_method' => $t->learning_method,
                'subjects' => $t->subjects->map(fn($s) => ['id' => $s->id, 'name' => $s->name]),
                'files' => $t->user?->files->map(fn($f) => [
                    'id' => $f->id,
                    'name' => $f->name,
                    'type' => $f->type,
                    'path_url' => $f->path_url,
                ]) ?? [],
            ];
        });

        $tutorsPaginator->setCollection($tutorsCollection);

        return response()->json([
            'tutors' => $tutorsPaginator,
        ]);
    }

    /**
     * Approve tutor verification (status = active)
     * 
     * @OA\Patch(
     *     path="/api/verify/tutor/approve",
     *     tags={"Admin - Tutor Verification"},
     *     summary="Approve tutor verification",
     *     description="Mengubah status tutor dari 'verify' menjadi 'active'. Hanya admin yang bisa mengakses.",
     *     security={{"sanctum":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"user_id"},
     *             @OA\Property(property="user_id", type="integer", example=2, description="ID user tutor yang akan di-approve")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Tutor berhasil diverifikasi",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string", example="Tutor berhasil diverifikasi dan diaktifkan"),
     *             @OA\Property(property="tutor", type="object",
     *                 @OA\Property(property="user_id", type="integer", example=2),
     *                 @OA\Property(property="status", type="string", example="active")
     *             )
     *         )
     *     ),
     *     @OA\Response(response=401, description="Unauthorized"),
     *     @OA\Response(response=403, description="Forbidden - Bukan role admin"),
     *     @OA\Response(response=404, description="Tutor not found atau status bukan 'verify'"),
     *     @OA\Response(response=422, description="Validation error"),
     *     @OA\Response(response=500, description="Server error")
     * )
     */
    public function approve(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:tutors,user_id',
        ]);

        DB::beginTransaction();
        try {
            $tutor = Tutor::where('user_id', $validated['user_id'])
                ->where('status', TutorStatusEnum::VERIFY->value)
                ->firstOrFail();

            $tutor->status = TutorStatusEnum::ACTIVE;
            $tutor->save();

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'Tutor berhasil diverifikasi dan diaktifkan',
                'tutor' => [
                    'user_id' => $tutor->user_id,
                    'status' => $tutor->status->value,
                ],
            ], 200);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal memverifikasi tutor: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Reject tutor verification (status = rejected)
     * 
     * @OA\Patch(
     *     path="/api/verify/tutor/reject",
     *     tags={"Admin - Tutor Verification"},
     *     summary="Reject tutor verification",
     *     description="Mengubah status tutor dari 'verify' menjadi 'rejected'. Admin dapat memberikan alasan penolakan.",
     *     security={{"sanctum":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"user_id"},
     *             @OA\Property(property="user_id", type="integer", example=2, description="ID user tutor yang akan ditolak"),
     *             @OA\Property(property="reason", type="string", example="Dokumen tidak lengkap", description="Alasan penolakan (optional, max 500 karakter)")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Tutor berhasil ditolak",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string", example="Tutor ditolak"),
     *             @OA\Property(property="tutor", type="object",
     *                 @OA\Property(property="user_id", type="integer", example=2),
     *                 @OA\Property(property="status", type="string", example="rejected"),
     *                 @OA\Property(property="reason", type="string", example="Dokumen tidak lengkap")
     *             )
     *         )
     *     ),
     *     @OA\Response(response=401, description="Unauthorized"),
     *     @OA\Response(response=403, description="Forbidden - Bukan role admin"),
     *     @OA\Response(response=404, description="Tutor not found atau status bukan 'verify'"),
     *     @OA\Response(response=422, description="Validation error"),
     *     @OA\Response(response=500, description="Server error")
     * )
     */
    public function reject(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:tutors,user_id',
            'reason' => 'nullable|string|max:500',
        ]);

        DB::beginTransaction();
        try {
            $tutor = Tutor::where('user_id', $validated['user_id'])
                ->where('status', TutorStatusEnum::VERIFY->value)
                ->firstOrFail();

            $tutor->status = TutorStatusEnum::REJECTED;
            $tutor->save();

            // Optional: Bisa ditambahkan notifikasi ke tutor dengan reason
            // Notification::send($tutor->user, new TutorRejectedNotification($validated['reason'] ?? null));

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'Tutor ditolak',
                'tutor' => [
                    'user_id' => $tutor->user_id,
                    'status' => $tutor->status->value,
                    'reason' => $validated['reason'] ?? null,
                ],
            ], 200);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal menolak tutor: ' . $e->getMessage(),
            ], 500);
        }
    }
}
