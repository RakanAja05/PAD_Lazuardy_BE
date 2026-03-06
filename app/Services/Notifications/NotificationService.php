<?php

namespace App\Services\Notifications;

use App\DTOs\ResponseDTO;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationService
{
    public function index(Request $request): ResponseDTO
    {
        $user = Auth::user();

        $notifications = $user->notifications()
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return new ResponseDTO([
            'status' => 'success',
            'message' => 'Notifications retrieved successfully',
            'data' => [
                'notifications' => $notifications->items(),
                'pagination' => [
                    'current_page' => $notifications->currentPage(),
                    'per_page' => $notifications->perPage(),
                    'total' => $notifications->total(),
                    'last_page' => $notifications->lastPage(),
                    'from' => $notifications->firstItem(),
                    'to' => $notifications->lastItem(),
                ],
            ],
        ], 200);
    }

    public function unreadCount(Request $request): ResponseDTO
    {
        $user = Auth::user();
        $count = $user->unreadNotifications()->count();

        return new ResponseDTO([
            'status' => 'success',
            'message' => 'Unread notifications count retrieved successfully',
            'data' => [
                'unread_count' => $count,
            ],
        ], 200);
    }

    public function markAsRead(Request $request, $id): ResponseDTO
    {
        $user = Auth::user();
        $notification = $user->notifications()->find($id);

        if (!$notification) {
            return new ResponseDTO([
                'status' => 'error',
                'message' => 'Notification not found',
                'errors' => [
                    'notification_id' => $id,
                ],
            ], 404);
        }

        $notification->markAsRead();

        return new ResponseDTO([
            'status' => 'success',
            'message' => 'Notification marked as read',
            'data' => [],
        ], 200);
    }

    public function markAllAsRead(Request $request): ResponseDTO
    {
        $user = Auth::user();
        $user->unreadNotifications->markAsRead();

        return new ResponseDTO([
            'status' => 'success',
            'message' => 'All notifications marked as read',
            'data' => [],
        ], 200);
    }

    public function delete(Request $request, $id): ResponseDTO
    {
        $user = Auth::user();
        $notification = $user->notifications()->find($id);

        if (!$notification) {
            return new ResponseDTO([
                'status' => 'error',
                'message' => 'Notification not found',
                'errors' => [
                    'notification_id' => $id,
                ],
            ], 404);
        }

        $notification->delete();

        return new ResponseDTO([
            'status' => 'success',
            'message' => 'Notification deleted successfully',
            'data' => [],
        ], 200);
    }

    public function deleteAllRead(Request $request): ResponseDTO
    {
        $user = Auth::user();
        $user->readNotifications()->delete();

        return new ResponseDTO([
            'status' => 'success',
            'message' => 'All read notifications deleted successfully',
            'data' => [],
        ], 200);
    }
}
