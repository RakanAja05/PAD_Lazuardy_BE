<?php

namespace App\Services;

use App\Enums\FileType;
use App\Models\File;
use App\Models\User;
use Exception;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class TutorService
{
    public function storeSubject(User $user, Collection $subjectData)
    {
        // $subjectData = $subjectData['subject_ids'];
        DB::beginTransaction();
        try {
            
            $subjectIds = $subjectData->get('subject_ids');

            $syncResult = $user->subjects()->sync($subjectIds);
            
            DB::commit();
            
            return $syncResult;
        } catch(Exception $e){
            
            DB::rollBack();
            
            throw $e;
        }
    }

    public function storeFile(User $user, Collection $fileData): bool
    {
        // Rule
        // ['key' => ['name'=>'sswsw', 'path_url'=>'sswsw'], 'key' => ['name'=>'sswsw', 'path_url'=>'sswsw']]
        $userId = $user->id;

        $fileTypes = [
            'cv' => FileType::CV->value,
            'ktp' => FileType::KTP->value,
            'ijazah' => FileType::IJAZAH->value,
            'certificate' => FileType::CERTIFICATE->value,
            'portofolio' => FileType::PORTOFOLIO->value,
        ];

        DB::beginTransaction();
        try {
            foreach($fileTypes as $key => $fileTypeValue)
            {
                $files = $fileData->get($key);
                if (is_array($files))
                {
                    $temp_arr = [];
                    foreach($files as $file)
                    {
                        if (isset($file['name']) && isset($file['path_url']))
                        {
                            array_push(
                                $temp_arr,
                                [
                                    'name' => $file['name'],
                                    'path_url' => $file['path_url'],
                                    'type' => $fileTypeValue,
                                ]
                            );
                        }
                    }
                    $user->files()->createMany($temp_arr);
                }
            }
            DB::commit();
            return true;
        } catch(Exception $e){
            DB::rollBack();
            throw $e;
        }
    }
}
