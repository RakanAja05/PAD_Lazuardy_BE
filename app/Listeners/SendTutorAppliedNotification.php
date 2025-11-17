<?php

namespace App\Listeners;

use App\Events\TutorAppliedEvent;
use App\Models\User;
use App\Notifications\TutorAppliedNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class SendTutorAppliedNotification implements ShouldQueue
{
    use InteractsWithQueue;

    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(TutorAppliedEvent $event): void
    {
        // Dapatkan semua admin (Flutter & Vue)
        $admins = User::where('role', 'admin')->get();

        // Kirim notifikasi ke semua admin
        foreach ($admins as $admin) {
            $admin->notify(new TutorAppliedNotification($event->tutorConfirm));
        }
    }
}
