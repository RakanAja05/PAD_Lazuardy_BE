<?php

namespace App\Services;

use App\Models\User;
use Exception;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ScheduleService
{
    public function storeScheduleTutor(User $user, Collection $scheduleDatas)
    {
        // Rules
        // $scheduleData = [['day'=>xxx, 'time'=>xxx]]
        $scheduleDatas = $scheduleDatas["schedules"];

        DB::beginTransaction();
        
        try {
            $schedules = $user->schedules()->upsert($scheduleDatas,['user_id', 'day', 'time']);
            DB::commit();
            return $schedules;

        } catch (Exception $e){
            DB::rollBack();
            throw $e;
        }
    }
}
