<?php

namespace App\Listeners;

use App\Events\TutorVerifiedEvent;
use App\Models\User;
use App\Notifications\TutorVerifiedNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class SendTutorVerifiedNotification implements ShouldQueue
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
    public function handle(TutorVerifiedEvent $event): void
    {
        // Dapatkan tutor dari tutorConfirm
        $tutor = User::find($event->tutorConfirm->tutor_user_id);

        // Kirim notifikasi ke tutor (Flutter & Vue)
        if ($tutor) {
            $tutor->notify(new TutorVerifiedNotification($event->tutorConfirm));
        }
    }
}
