<?php

namespace App\Auth\Controllers;

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

    public function createNotification() {}

    public function updateNotification() {}

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
