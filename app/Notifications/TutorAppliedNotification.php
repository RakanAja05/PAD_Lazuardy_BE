<?php

namespace App\Notifications;

use App\Models\TutorConfirm;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class TutorAppliedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public TutorConfirm $tutorConfirm;

    /**
     * Create a new notification instance.
     */
    public function __construct(TutorConfirm $tutorConfirm)
    {
        $this->tutorConfirm = $tutorConfirm;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database', 'broadcast'];
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'tutor_confirm_id' => $this->tutorConfirm->id,
            'tutor_user_id' => $this->tutorConfirm->tutor_user_id,
            'student_user_id' => $this->tutorConfirm->student_user_id,
            'reason' => $this->tutorConfirm->reason,
            'title' => 'Pendaftaran Tutor Baru',
            'message' => 'Tutor baru telah mendaftar dan menunggu verifikasi dari admin.',
            'type' => 'tutor_applied',
            'created_at' => now()->toIso8601String(),
        ];
    }

    /**
     * Get the broadcastable representation of the notification.
     */
    public function toBroadcast(object $notifiable): BroadcastMessage
    {
        return new BroadcastMessage($this->toArray($notifiable));
    }
}
