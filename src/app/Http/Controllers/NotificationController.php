<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    /**
     * Lista svih obaveštenja ulogovanog korisnika.
     */
    public function index(Request $request): JsonResponse
    {
        $notifications = $request->user()->notifications()->paginate(15);

        return response()->json([
            'data' => $notifications,
        ]);
    }

    /**
     * Lista samo nepročitanih obaveštenja.
     */
    public function unread(Request $request): JsonResponse
    {
        return response()->json([
            'data' => $request->user()->unreadNotifications,
        ]);
    }

    /**
     * Označava obaveštenje kao pročitano.
     */
    public function markAsRead(Request $request, string $id): JsonResponse
    {
        $notification = $request->user()
            ->notifications()
            ->where('id', $id)
            ->firstOrFail();

        $notification->markAsRead();

        return response()->json([
            'message' => 'Obaveštenje je označeno kao pročitano.',
        ]);
    }
}
