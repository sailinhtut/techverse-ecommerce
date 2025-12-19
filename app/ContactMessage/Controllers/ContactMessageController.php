<?php

namespace App\ContactMessage\Controllers;

use App\ContactMessage\Models\ContactMessage;
use Exception;
use Illuminate\Http\Request;

class ContactMessageController
{
    public function createMessage(Request $request)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|email|max:255',
                'message' => 'required|string',
            ]);

            ContactMessage::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'message' => $validated['message'],
                'status' => 'new',
            ]);

            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'Message sent successfully.'
                ], 201);
            }

            return redirect()->back()->with('success', 'Message sent successfully.');
        } catch (Exception $e) {
            return handleErrors($e);
        }
    }


    public function viewAdminContactMessageListPage(Request $request)
    {
        $sortBy = $request->get('sortBy', 'last_updated');
        $orderBy = $request->get('orderBy', 'desc');
        $perPage = $request->get('perPage', 20);
        $search = $request->get('query', null);

        $query = ContactMessage::query();

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('message', 'like', "%{$search}%")
                    ->orWhere('status', 'like', "%{$search}%")
                    ->orWhere('id', 'like', "%{$search}%");
            });
        }

        switch ($sortBy) {
            case 'last_updated':
                $query->orderBy('updated_at', $orderBy)
                    ->orderBy('id', $orderBy);
                break;

            case 'last_created':
                $query->orderBy('created_at', $orderBy)->orderBy('id', $orderBy);
                break;

            default:
                $query->orderBy('updated_at', 'desc')
                    ->orderBy('id', 'desc');
        }

        $contact_messages = $query->paginate($perPage);
        $contact_messages->appends(request()->query());

        $contact_messages->getCollection()->transform(function ($class) {
            return $class->jsonResponse();
        });


        return view('pages.admin.dashboard.contact_message.contact_message_list', [
            'messages'   => $contact_messages,
        ]);
    }

    public function updateMessage(Request $request, int $id)
    {
        $status = $request->get('status', 'new');

        $message = ContactMessage::find($id);

        if (!$message) {
            return redirect()->back()->with('error', 'No message found');
        }

        $message->status = $status;
        $message->save();

        return redirect()->back()->with('success', 'Message updated successfully');
    }

    public function deleteMessage(int $id)
    {
        $message = ContactMessage::find($id);

        if (!$message) {
            return redirect()->back()->with('error', 'Message not found');
        }

        $message->delete();

        return redirect()->back()->with('success', 'Message deleted successfully');
    }


    public function deleteSelectedMessages(Request $request)
    {
        try {
            $ids = $request->get('ids', []);

            if (empty($ids)) {
                return redirect()->back()->with('error', 'No messages selected for deletion');
            }

            ContactMessage::whereIn('id', $ids)->delete();

            return redirect()->back()->with('success', 'Selected messages deleted successfully');
        } catch (Exception $e) {
            return handleErrors($e);
        }
    }


    public function deleteAllMessages()
    {
        try {
            ContactMessage::query()->delete();

            return redirect()->back()->with('success', 'All messages deleted successfully');
        } catch (Exception $e) {
            return handleErrors($e);
        }
    }
}
