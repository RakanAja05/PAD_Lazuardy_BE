<?php

namespace App\Http\Controllers\Notifications;

use App\Http\Controllers\Controller;
use App\Services\Notifications\NotificationService;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function __construct(private readonly NotificationService $notificationService)
    {
    }

    public function index(Request $request)
    {
        $result = $this->notificationService->index($request);

        return response()->json($result->payload, $result->code);
    }

    public function unreadCount(Request $request)
    {
        $result = $this->notificationService->unreadCount($request);

        return response()->json($result->payload, $result->code);
    }

    public function markAsRead(Request $request, $id)
    {
        $result = $this->notificationService->markAsRead($request, $id);

        return response()->json($result->payload, $result->code);
    }

    public function markAllAsRead(Request $request)
    {
        $result = $this->notificationService->markAllAsRead($request);

        return response()->json($result->payload, $result->code);
    }

    public function delete(Request $request, $id)
    {
        $result = $this->notificationService->delete($request, $id);

        return response()->json($result->payload, $result->code);
    }

    public function deleteAllRead(Request $request)
    {
        $result = $this->notificationService->deleteAllRead($request);

        return response()->json($result->payload, $result->code);
    }
}
