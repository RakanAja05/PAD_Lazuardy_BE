<?php

namespace App\Services\Tutors;

use App\DTOs\ResponseDTO;
use App\Http\Requests\StoreTutorApplicationRequest;
use App\Services\TutorService;
use Exception;
use Illuminate\Support\Facades\DB;

class TutorApplicationService
{
    public function store(StoreTutorApplicationRequest $request): ResponseDTO
    {
        $request->validated();
        $user = $request->user()->load(['tutor']);
        $tutor = $user->tutor;

        $tutorData = $request->only([
            'experience', 'organization',
        ]);

        $fileData = $request->only([
            'cv', 'ktp', 'ijazah',
            'certificate', 'portofolio',
        ]);

        $tutorService = new TutorService();
        DB::beginTransaction();
        try {
            $tutor->update($tutorData);
            $tutorService->storeTutorFile($user, collect($fileData));
            DB::commit();

            return new ResponseDTO([
                'status' => 'success',
                'message' => 'Berhasil menyelesaikan formulir pendaftaran tutor',
                'data' => [],
            ], 200);
        } catch (Exception $e) {
            DB::rollBack();

            return new ResponseDTO([
                'status' => 'error',
                'message' => $e->getMessage(),
                'errors' => [
                    'detail' => $e->getMessage(),
                ],
            ], 500);
        }
    }
}
