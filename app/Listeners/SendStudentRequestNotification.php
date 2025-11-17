<?php

namespace App\Listeners;

use App\Events\StudentRequestEvent;
use App\Models\User;
use App\Notifications\StudentRequestNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class SendStudentRequestNotification implements ShouldQueue
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
    public function handle(StudentRequestEvent $event): void
    {
        // Kirim notifikasi ke semua admin (Flutter & Vue)
        // Admin yang akan assign tutor ke student
        $admins = User::where('role', 'admin')->get();

        foreach ($admins as $admin) {
            $admin->notify(new StudentRequestNotification($event->order));
        }
    }
}
