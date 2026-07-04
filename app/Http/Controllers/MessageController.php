<?php

namespace App\Http\Controllers;

use App\Models\Message;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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
            ->withCount(['sentMessages as unread_count' => function($q) {
                $q->where('receiver_id', Auth::id())->where('is_read', false);
            }])
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

        $messages = Message::where(function($q) use ($user) {
                $q->where('sender_id', Auth::id())
                  ->where('receiver_id', $user->id);
            })->orWhere(function($q) use ($user) {
                $q->where('sender_id', $user->id)
                  ->where('receiver_id', Auth::id());
            })
            ->orderBy('created_at')
            ->get();

        return view('messages.conversation', compact('messages', 'user'));
    }

    // Send a message
    public function send(Request $request)
    {
        $request->validate([
            'message'     => 'required|string|max:1000',
            'receiver_id' => 'required|exists:users,id',
        ]);

        Message::create([
            'sender_id'   => Auth::id(),
            'receiver_id' => $request->receiver_id,
            'message'     => $request->input('message'),
            'is_read'     => false,
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

        $messages = Message::where(function($q) use ($admin) {
                $q->where('sender_id', Auth::id())
                  ->where('receiver_id', $admin->id);
            })->orWhere(function($q) use ($admin) {
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