<?php

namespace App\Auth\Services;

use App\Auth\Models\Notification;
use Exception;
use Illuminate\Http\Request;

class NotificationService
{
    public static function getNotifications()
    {
        try {
            if (!auth()->check()) abort(403, 'Please log in to continue');
            $notifications = Notification::where('user_id', auth()->id())->orderBy('id', 'desc')->paginate(20);
            $notifications->getCollection()->transform(function ($notification) {
                return $notification->jsonResponse();
            });
            return $notifications;
        } catch (Exception $e) {
            throw ($e);
        }
    }

    public static function createNotification() {}

    public static function updateNotification() {}

    public static function deleteNotification(int $id): bool
    {
        try {
            $notification = Notification::find($id);
            if (!$notification) abort(404, 'No notification found');

            // if ($notification->image) {
            //     Storage::disk('public')->delete($notification->image);
            // }

            $notification->delete();
            return true;
        } catch (Exception $e) {
            throw ($e);
        }
    }
}
