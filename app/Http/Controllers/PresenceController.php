<?php

namespace App\Http\Controllers;

use App\Models\Presence;
use Illuminate\Http\Request;

class PresenceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $user = $request->user()->load([
            'takenSchedules.student.student.class',
            'takenSchedules.subject',
            'takenSchedules.scheduleTutor',
        ]);

        $data = $user->takenSchedules
                    ->map(function($takenSchedule){
                        $student = $takenSchedule->student;
                        
                        return [
                            'taken_schedule_id' => $takenSchedule->id,
                            'student_id' => $student->id,
                            'student_name' => $student->name,
                            'student_telephone_number' => $student->telephone_number,
                            'student_latitude' => $student->latitude,
                            'student_longitude' => $student->longitude,
                            'student_class' => $student->student->class->name,
                            'subject_id' => $takenSchedule->subject->id,
                            'subject_name' => $takenSchedule->subject->name,
                            'date' => $takenSchedule->date,
                            'status' => $takenSchedule->status,
                        ];
                    });
        
        return response()->json([
            'status' => 'success',
            'data' => $data
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'taken_schedule_id' => ['required', 'integer', 'exists:taken_schedules,id'],
            'student_user_id' => ['required', 'integer', 'exists:users,id,role,student'],
            'material' => ['required', 'string'],
            'evaluation' => ['required', 'string'],
            'grade' => ['required', 'integer'],
            'photo' => ['required', 'file', 'mimes:png,jpg, pdf, svg, webp'],
        ]);

        $user = $request->user();

        $presenceData = $request->only([
            'taken_schedule_id', 'student_user_id', 
            'material', 'evaluation', 'grade',
        ]);

        $presenceData['tutor_user_id'] = $user->id;

        if($request->hasFile('photo')){
            $file = $request->file('photo');
            $path = $file->store('uploads', 'public');
            $presenceData['pbm_image_url'] = $path;
            
            Presence::create($presenceData);

            return response()->json([
                'status' => 'success',
                'message' => 'Presensi berhasil',
            ], 201);
        }
        return response()->json([
            'status' => 'failed',
            'message' => 'presensi gagal',
        ], 401);
    }
}
