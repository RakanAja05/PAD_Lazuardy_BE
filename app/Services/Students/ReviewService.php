<?php

namespace App\Services\Students;

use App\DTOs\ResponseDTO;
use App\Enums\RatingOptionEnum;
use App\Enums\RoleEnum;
use App\Models\Review;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Enum;

class ReviewService
{
    public function index(Request $request): ResponseDTO
    {
        $user = $request->user()->load([
            'studentPackageStudents.tutor.subjects.class',
            'studentPackageStudents.subject',
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

        return new ResponseDTO([
            'status' => 'success',
            'message' => 'Data daftar tutor berhasil terkirim',
            'data' => $tutorsToReview,
        ], 200);
    }

    public function storeOrUpdate(Request $request): ResponseDTO
    {
        $request->validate([
            'tutor_id' => [
                'required',
                'integer',
                Rule::exists('users', 'id')->where('role', RoleEnum::TUTOR->value),
            ],
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

            return new ResponseDTO([
                'status' => 'success',
                'message' => 'Review berhasil terkirim',
                'data' => [],
            ], 201);
        } catch (Exception $e) {
            return new ResponseDTO([
                'status' => 'error',
                'message' => 'Review gagal terkirim: ' . $e->getMessage(),
                'errors' => [
                    'detail' => $e->getMessage(),
                ],
            ], 401);
        }
    }

    public function show(Request $request): ResponseDTO
    {
        $request->validate([
            'tutor_id' => [
                'required',
                'integer',
                Rule::exists('users', 'id')->where('role', RoleEnum::TUTOR->value),
            ],
        ]);
        $student = $request->user();

        $reviewData = Review::where('from_user_id', $student->id)
            ->where('to_user_id', $request->tutor_id)
            ->first();

        return new ResponseDTO([
            'status' => 'success',
            'message' => 'Berhasil mengambil detail review',
            'data' => $reviewData,
        ], 200);
    }
}
