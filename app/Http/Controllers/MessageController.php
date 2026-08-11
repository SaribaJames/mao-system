<?php

namespace App\Http\Controllers;

use App\Models\Message;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class MessageController extends Controller
{
    // Admin/Staff: view all conversations
    public function index()
    {
        // Get all users who have sent messages
        $conversations = User::whereHas('sentMessages')
            ->orWhereHas('receivedMessages')
            ->where('id', '!=', Auth::id())
            ->whereHas('role', fn($q) => $q->where('name', 'barangay_user'))
            ->withCount([
                'sentMessages as unread_count' => function ($q) {
                    $q->where('receiver_id', Auth::id())->where('is_read', false);
                }
            ])
            ->get();

        return view('messages.index', compact('conversations'));
    }

    // View conversation with a specific user
    public function conversation(User $user)
    {
        // Mark messages as read
        Message::where('sender_id', $user->id)
            ->where('receiver_id', Auth::id())
            ->where('is_read', false)
            ->update(['is_read' => true]);

        $messages = Message::where(function ($q) use ($user) {
            $q->where('sender_id', Auth::id())
                ->where('receiver_id', $user->id);
        })->orWhere(function ($q) use ($user) {
            $q->where('sender_id', $user->id)
                ->where('receiver_id', Auth::id());
        })
            ->orderBy('created_at')
            ->get();

        return view('messages.conversation', compact('messages', 'user'));
    }

    // Send a message (with optional file attachment)
    public function send(Request $request)
    {
        \Log::info('CHAT SEND DEBUG', [
            'has_message' => $request->filled('message'),
            'has_file' => $request->hasFile('attachment'),
            'all_input' => $request->all(),
        ]);
        $request->validate([
            'message' => 'nullable|string|max:1000',
            'receiver_id' => 'required|exists:users,id',
            'attachment' => 'nullable|file|max:10240|mimes:jpg,jpeg,png,gif,pdf,doc,docx,xls,xlsx',
        ]);

        // Require at least a message OR an attachment — not both empty
        if (!$request->filled('message') && !$request->hasFile('attachment')) {
            return back()->with('error', 'Please type a message or attach a file.');
        }

        $attachmentPath = null;
        $attachmentName = null;
        $attachmentType = null;
        $attachmentSize = null;

        if ($request->hasFile('attachment')) {
            $file = $request->file('attachment');
            $attachmentPath = $file->store('chat-attachments', 'public');
            $attachmentName = $file->getClientOriginalName();
            $attachmentSize = $file->getSize();
            \Log::info('ATTACHMENT STORE DEBUG', [
                'attachment_path' => $attachmentPath,
                'attachment_name' => $attachmentName,
            ]);

            $ext = strtolower($file->getClientOriginalExtension());
            if (in_array($ext, ['jpg', 'jpeg', 'png', 'gif'])) {
                $attachmentType = 'image';
            } elseif ($ext === 'pdf') {
                $attachmentType = 'pdf';
            } else {
                $attachmentType = 'document';
            }
        }

        Message::create([
            'sender_id' => Auth::id(),
            'receiver_id' => $request->receiver_id,
            'message' => $request->input('message'),
            'attachment_path' => $attachmentPath,
            'attachment_name' => $attachmentName,
            'attachment_type' => $attachmentType,
            'attachment_size' => $attachmentSize,
            'is_read' => false,
        ]);

        return back()->with('success', 'Message sent!');
    }

    // Barangay Rep: open chat with admin
    public function chat()
    {
        // Find admin user
        $admin = User::whereHas('role', fn($q) => $q->where('name', 'admin'))
            ->first();

        if (!$admin) {
            return back()->with('error', 'No admin available.');
        }

        // Mark messages as read
        Message::where('sender_id', $admin->id)
            ->where('receiver_id', Auth::id())
            ->where('is_read', false)
            ->update(['is_read' => true]);

        $messages = Message::where(function ($q) use ($admin) {
            $q->where('sender_id', Auth::id())
                ->where('receiver_id', $admin->id);
        })->orWhere(function ($q) use ($admin) {
            $q->where('sender_id', $admin->id)
                ->where('receiver_id', Auth::id());
        })
            ->orderBy('created_at')
            ->get();

        return view('messages.chat', compact('messages', 'admin'));
    }

    // Get unread message count (for notifications)
    public function unreadCount()
    {
        $count = Message::where('receiver_id', Auth::id())
            ->where('is_read', false)
            ->count();

        return response()->json(['count' => $count]);
    }
}