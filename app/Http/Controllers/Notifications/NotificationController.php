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

    /**
     * @OA\Get(
     *     path="/api/notifications",
     *     tags={"Notifications"},
     *     summary="List notifications",
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(response=200, description="Notifications", @OA\JsonContent(ref="#/components/schemas/StandardSuccess"))
     * )
     */
    public function index(Request $request)
    {
        $result = $this->notificationService->index($request);

        return response()->json($result->payload, $result->code);
    }

    /**
     * @OA\Get(
     *     path="/api/notifications/unread-count",
     *     tags={"Notifications"},
     *     summary="Unread notifications count",
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(response=200, description="Unread count", @OA\JsonContent(ref="#/components/schemas/StandardSuccess"))
     * )
     */
    public function unreadCount(Request $request)
    {
        $result = $this->notificationService->unreadCount($request);

        return response()->json($result->payload, $result->code);
    }

    /**
     * @OA\Patch(
     *     path="/api/notifications/{id}/read",
     *     tags={"Notifications"},
     *     summary="Mark one as read",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="string")),
     *     @OA\Response(response=200, description="Updated", @OA\JsonContent(ref="#/components/schemas/StandardSuccess"))
     * )
     */
    public function markAsRead(Request $request, $id)
    {
        $result = $this->notificationService->markAsRead($request, $id);

        return response()->json($result->payload, $result->code);
    }

    /**
     * @OA\Patch(
     *     path="/api/notifications/read-all",
     *     tags={"Notifications"},
     *     summary="Mark all as read",
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(response=200, description="Updated", @OA\JsonContent(ref="#/components/schemas/StandardSuccess"))
     * )
     */
    public function markAllAsRead(Request $request)
    {
        $result = $this->notificationService->markAllAsRead($request);

        return response()->json($result->payload, $result->code);
    }

    /**
     * @OA\Delete(
     *     path="/api/notifications/{id}",
     *     tags={"Notifications"},
     *     summary="Delete notification",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="string")),
     *     @OA\Response(response=200, description="Deleted", @OA\JsonContent(ref="#/components/schemas/StandardSuccess"))
     * )
     */
    public function delete(Request $request, $id)
    {
        $result = $this->notificationService->delete($request, $id);

        return response()->json($result->payload, $result->code);
    }

    /**
     * @OA\Delete(
     *     path="/api/notifications/read-all",
     *     tags={"Notifications"},
     *     summary="Delete all read notifications",
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(response=200, description="Deleted", @OA\JsonContent(ref="#/components/schemas/StandardSuccess"))
     * )
     */
    public function deleteAllRead(Request $request)
    {
        $result = $this->notificationService->deleteAllRead($request);

        return response()->json($result->payload, $result->code);
    }
}
