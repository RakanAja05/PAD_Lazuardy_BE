<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTutorApplicationRequest;
use App\Models\ClassModel;
use App\Models\Subject;
use App\Services\TutorService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TutorApplicationController extends Controller
{

    public function index()
    {
        $classes = ClassModel::all(['id', 'name']);
        $subjects = Subject::all('id', 'name');
        $curriculums = Subject::all('id', 'name');

        return response()->json([
            'classes' => $classes,
            'subjects' => $subjects,
            'curriculums' => $curriculums,
        ], 200);
    }

    public function store(StoreTutorApplicationRequest $request)
    {
        $request->validated();
        $user = $request->user()->load(['tutor']);
        $tutor = $user->tutor;

        $tutorData = $request->only([
            'experience', 'organization',
        ]);

        $subjectData = $request['subject_ids'];

        $fileData = $request->only([
            'cv', 'ktp', 'ijazah',
            'certificate', 'portofolio',
        ]);

        $tutorService = new TutorService;
        DB::beginTransaction();
        try {
            $tutor->update($tutorData);
            $user->subjects()->sync($subjectData);
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
