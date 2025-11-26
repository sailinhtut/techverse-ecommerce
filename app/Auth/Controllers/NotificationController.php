<?php

namespace App\Auth\Controllers;

use App\Auth\Models\Notification;
use App\Auth\Services\NotificationService;
use Exception;
use Illuminate\Http\Request;

class NotificationController
{
    public function getNotifications()
    {
        try {
            $notifications = NotificationService::getNotifications();
            return view('pages.user.dashboard.notification', [
                'notifications' => $notifications
            ]);
        } catch (Exception $e) {
            return handleErrors($e);
        }
    }

    public function getUnreadCount()
    {
        try {
            $count = Notification::where('user_id', auth()->id())
                ->whereNull('read_at')
                ->count();

            return response()->json([
                'success' => true,
                'data' => [
                    'unread_count' => $count
                ]
            ]);
        } catch (Exception $e) {
            return handleErrors($e);
        }
    }


    public function markReadNotifications(Request $request)
    {
        try {
            $validated = $request->validate([
                'ids' => 'nullable|array',
                'ids.*' => 'integer'
            ]);

            $ids = $validated['ids'] ?? [];

            if (empty($ids)) {
                $updated = Notification::where('user_id', auth()->id())
                    ->whereNull('read_at')
                    ->update(['read_at' => now()]);
            } else {
                // Mark only selected ids
                $updated = Notification::whereIn('id', $ids)
                    ->where('user_id', auth()->id())
                    ->update(['read_at' => now()]);
            }

            return response()->json([
                'success' => true,
                'message' => empty($ids)
                    ? 'All unread notifications marked as read'
                    : 'Selected notifications marked as read',
                'data' => [
                    'updated_count' => $updated,
                ]
            ]);
        } catch (Exception $e) {
            return handleErrors($e);
        }
    }



    public function deleteNotification(Request $request, $id)
    {
        try {
            $deleted = NotificationService::deleteNotification(intval($id));
            if (!$deleted) {
                abort('500', 'Cannot delete notification');
            }
            return redirect()->back()->with('success', 'Notification is deleted');
        } catch (Exception $e) {
            return handleErrors($e);
        }
    }
}
