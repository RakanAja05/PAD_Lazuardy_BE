<?php

namespace App\Services\Admin;

use App\DTOs\ResponseDTO;
use App\Enums\TutorStatusEnum;
use App\Models\Tutor;
use Exception;
use Illuminate\Support\Facades\DB;

class TutorVerifyService
{
    public function index(): ResponseDTO
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

        return new ResponseDTO([
            'status' => 'success',
            'message' => 'Berhasil mengambil data tutor',
            'data' => [
                'tutors' => $tutorsPaginator,
            ],
        ], 200);
    }

    public function approve(array $validated): ResponseDTO
    {
        DB::beginTransaction();
        try {
            $tutor = Tutor::where('user_id', $validated['user_id'])
                ->where('status', TutorStatusEnum::VERIFY->value)
                ->firstOrFail();

            $tutor->status = TutorStatusEnum::ACTIVE;
            $tutor->save();

            DB::commit();

            return new ResponseDTO([
                'status' => 'success',
                'message' => 'Tutor berhasil diverifikasi dan diaktifkan',
                'data' => [
                    'tutor' => [
                        'user_id' => $tutor->user_id,
                        'status' => $tutor->status->value,
                    ],
                ],
            ], 200);
        } catch (Exception $e) {
            DB::rollBack();

            return new ResponseDTO([
                'status' => 'error',
                'message' => 'Gagal memverifikasi tutor: ' . $e->getMessage(),
                'errors' => [
                    'detail' => $e->getMessage(),
                ],
            ], 500);
        }
    }

    public function reject(array $validated): ResponseDTO
    {
        DB::beginTransaction();
        try {
            $tutor = Tutor::where('user_id', $validated['user_id'])
                ->where('status', TutorStatusEnum::VERIFY->value)
                ->firstOrFail();

            $tutor->status = TutorStatusEnum::REJECTED;
            $tutor->save();

            DB::commit();

            return new ResponseDTO([
                'status' => 'success',
                'message' => 'Tutor ditolak',
                'data' => [
                    'tutor' => [
                        'user_id' => $tutor->user_id,
                        'status' => $tutor->status->value,
                        'reason' => $validated['reason'] ?? null,
                    ],
                ],
            ], 200);
        } catch (Exception $e) {
            DB::rollBack();

            return new ResponseDTO([
                'status' => 'error',
                'message' => 'Gagal menolak tutor: ' . $e->getMessage(),
                'errors' => [
                    'detail' => $e->getMessage(),
                ],
            ], 500);
        }
    }
}
