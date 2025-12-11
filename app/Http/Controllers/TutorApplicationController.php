<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTutorApplicationRequest;
use App\Services\TutorService;
use Exception;
use Illuminate\Support\Facades\DB;

class TutorApplicationController extends Controller
{
    public function store(StoreTutorApplicationRequest $request)
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

        $tutorService = new TutorService;
        DB::beginTransaction();
        try {
            $tutor->update($tutorData);
            $tutorService->storeTutorFile($user, collect($fileData));
            DB::commit();
            return response()->json([
                'status' => 'success',
                'message' => "Berhasil menyelesaikan formulir pendaftaran tutor",
            ], 200);
        } catch(Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ], 500);
        }
    }
}
