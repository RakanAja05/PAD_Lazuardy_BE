<?php

namespace App\Services;

use App\Enums\FileTypeEnum;
use App\Models\Tutor;
use App\Models\User;
use Exception;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class TutorService
{
    public function storeTutorFile(User $user, Collection $fileset)
    {
        $userId = $user->id;

        $types = [
            FileTypeEnum::CV->value,
            FileTypeEnum::CERTIFICATE->value,
            FileTypeEnum::IJAZAH->value,
            FileTypeEnum::KTP->value,
            FileTypeEnum::PORTOFOLIO->value,
        ];

        $temp_arr = [];
        foreach($types as $type)
        {
            $files = $fileset->get($type, null);
            if(!$files) continue;

            foreach($files as $file)
            {
                array_push($temp_arr, [
                    'name' => $file['name'],
                    'path_url' => $file['path_url'],
                    'type' => $type
                ]);
            }
        }
        return $user->files()->createMany($temp_arr);
    }


    public function storeScheduleTutor(User $user, Collection $schedules)
    {
        return $user->schedules()->upsert($schedules->get("schedules"),['user_id', 'day', 'time']);
    }


    public function showTutorProfile(User $user, Tutor $tutor)
    {
        $data = [
            'education' => $tutor->education,
            'salary' => $tutor->salary,
            'price' => $tutor->price,
            'description' => $tutor->description,
            'qualification' => $tutor->qualification,
            'experience' => $tutor->experience,
            'organization' => $tutor->organization,
            'learning_method' => $tutor->learning_method,
            'course_mode' => $tutor->course_mode->displayName(),
            'status' => $tutor->status->displayName(),
            'rank' => $tutor->rank->displayName(),
            'sanction_amount' => $tutor->sanction_amount,
            
            'schedules' => $user->schedules
        ];

        return $data;
    }
}
