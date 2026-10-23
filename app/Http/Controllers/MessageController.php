<?php

namespace App\Http\Controllers;

use App\Models\Message;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class MessageController extends Controller
{
    // Admin/Staff: conversations with barangay reps, plus anyone else
    // (staff, admin) they have messaged directly
    public function index()
    {
        $me = Auth::id();
        // The "has messaged either way" test must be grouped. Left ungrouped,
        // SQL binds AND tighter than OR, so the sent-messages branch escaped
        // both the exclude-self and barangay-rep filters — the admin saw their
        // own row and every staff coordinator in the list.
        $conversations = User::where(function ($q) use ($me) {
                $q->where(function ($q) {
                    // Every rep who has messaged anyone (admin oversight)
                    $q->whereHas('role', fn($r) => $r->where('name', 'barangay_user'))
                      ->where(fn($q) => $q->whereHas('sentMessages')->orWhereHas('receivedMessages'));
                })->orWhere(function ($q) use ($me) {
                    // Anyone this user has messaged directly, either way
                    $q->whereHas('sentMessages', fn($m) => $m->where('receiver_id', $me))
                      ->orWhereHas('receivedMessages', fn($m) => $m->where('sender_id', $me));
                });
            })
            ->where('id', '!=', $me)
            ->with(['role', 'barangayAccount.barangay'])
            ->withCount([
                'sentMessages as unread_count' => function ($q) {
                    $q->where('receiver_id', Auth::id())->where('is_read', false);
                }
            ])
            ->get();

        $contacts = $this->contactsFor(Auth::user());

        return view('messages.index', compact('conversations', 'contacts'));
    }

    /**
     * Everyone the user can start a conversation with, for the
     * "New Message" picker. Admin/Staff can message any active Admin,
     * Staff or Barangay Rep. Barangay Reps can message the Admin or any
     * staff coordinator (coordinator threads still go through the Admin,
     * so the oversight view keeps working).
     */
    protected function contactsFor(User $user)
    {
        $roles = $user->isBarangayUser()
            ? ['admin', 'staff']
            : ['admin', 'staff', 'barangay_user'];

        return User::where('id', '!=', $user->id)
            ->where('status', 'active')
            ->whereHas('role', fn($q) => $q->whereIn('name', $roles))
            ->with(['role', 'barangayAccount.barangay'])
            ->orderBy('name')
            ->get()
            ->map(fn(User $u) => [
                'user' => $u,
                'label' => self::roleLabel($u),
                'url' => $user->isBarangayUser()
                    ? route('messages.rep-conversation', $u)
                    : route('messages.conversation', $u),
            ]);
    }

    public static function roleLabel(User $u): string
    {
        return match ($u->role?->name) {
            'admin' => 'MAO Admin',
            'staff' => 'MAO Staff / Program Coordinator',
            'barangay_user' => 'Barangay Rep' . ($u->barangayAccount?->barangay?->name ? ' — ' . $u->barangayAccount->barangay->name : ''),
            default => ucfirst(str_replace('_', ' ', $u->role?->name ?? 'User')),
        };
    }

    // View conversation with a specific user (Admin/Staff viewing a rep, or vice versa)
    public function conversation(User $user)
    {
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
        })->orWhere(function ($q) use ($user) {
            $q->where('sender_id', $user->id)
                ->where('mentioned_user_id', Auth::id());
        })
            ->orderBy('created_at')
            ->get();

        return view('messages.conversation', compact('messages', 'user'));
    }

    // Send a message (with optional file attachment)
    public function send(Request $request)
    {
        $request->validate([
            'message' => 'nullable|string|max:1000',
            'receiver_id' => 'required|exists:users,id',
            'mentioned_user_id' => 'nullable|exists:users,id',
            'attachment' => 'nullable|file|max:10240|mimes:jpg,jpeg,png,gif,pdf,doc,docx,xls,xlsx',
        ]);

        if (!$request->filled('message') && !$request->hasFile('attachment')) {
            return back()->with('error', 'Please type a message or attach a file.');
        }

        $attachmentPath = null;
        $attachmentName = null;
        $attachmentType = null;
        $attachmentSize = null;

        if ($request->hasFile('attachment')) {
            $file = $request->file('attachment');
            $attachmentPath = $file->store('chat-attachments', 'cloudinary');
            $attachmentName = $file->getClientOriginalName();
            $attachmentSize = $file->getSize();

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
            'mentioned_user_id' => $request->mentioned_user_id,
            'message' => $request->input('message'),
            'attachment_path' => $attachmentPath,
            'attachment_name' => $attachmentName,
            'attachment_type' => $attachmentType,
            'attachment_size' => $attachmentSize,
            'is_read' => false,
        ]);

        return redirect()->back()->with('success', 'Message sent!');
    }

    // Barangay Rep: inbox listing every thread — Admin, plus any coordinator
    // they've been @mentioned to and who has replied
    public function repInbox()
    {
        $admin = User::whereHas('role', fn($q) => $q->where('name', 'admin'))->first();

        // Coordinators who have sent this rep a direct message
        // (i.e. replied after being @mentioned), plus coordinators this rep
        // has started a thread with from the "New Message" picker
        $repliedIds = Message::where('receiver_id', Auth::id())
            ->whereHas('sender.role', fn($q) => $q->where('name', 'staff'))
            ->pluck('sender_id');
        $mentionedIds = Message::where('sender_id', Auth::id())
            ->whereNotNull('mentioned_user_id')
            ->whereHas('mentionedUser.role', fn($q) => $q->where('name', 'staff'))
            ->pluck('mentioned_user_id');
        $coordinatorIds = $repliedIds->merge($mentionedIds)->unique();

        $coordinators = User::whereIn('id', $coordinatorIds)->get();

        $threads = collect();

        if ($admin) {
            $unread = Message::where('sender_id', $admin->id)
                ->where('receiver_id', Auth::id())
                ->where('is_read', false)
                ->count();
            $threads->push(['user' => $admin, 'label' => 'MAO Admin', 'unread' => $unread]);
        }

        foreach ($coordinators as $coordinator) {
            $unread = Message::where('sender_id', $coordinator->id)
                ->where('receiver_id', Auth::id())
                ->where('is_read', false)
                ->count();
            $threads->push(['user' => $coordinator, 'label' => 'Program Coordinator', 'unread' => $unread]);
        }

        $contacts = $this->contactsFor(Auth::user());

        return view('messages.rep-inbox', compact('threads', 'contacts'));
    }

    // Barangay Rep: view/send within one specific thread (Admin or a coordinator)
    public function repConversation(User $user)
    {
        $admin = User::whereHas('role', fn($q) => $q->where('name', 'admin'))->first();

        Message::where('sender_id', $user->id)
            ->where('receiver_id', Auth::id())
            ->where('is_read', false)
            ->update(['is_read' => true]);

        $isAdminThread = $admin && $user->id === $admin->id;

        if ($isAdminThread) {
            $messages = Message::where(function ($q) use ($user) {
                $q->where('sender_id', Auth::id())->where('receiver_id', $user->id);
            })->orWhere(function ($q) use ($user) {
                $q->where('sender_id', $user->id)->where('receiver_id', Auth::id());
            })->orderBy('created_at')->get();
        } else {
            // Coordinator thread: rep's messages that mentioned this coordinator,
            // plus the coordinator's direct replies
            $messages = Message::where(function ($q) use ($user) {
                $q->where('sender_id', Auth::id())->where('mentioned_user_id', $user->id);
            })->orWhere(function ($q) use ($user) {
                $q->where('sender_id', $user->id)->where('receiver_id', Auth::id());
            })->orderBy('created_at')->get();
        }

        $mentionable = User::whereHas('role', fn($q) => $q->where('name', 'staff'))
            ->orderBy('name')
            ->get(['id', 'name']);

        return view('messages.rep-conversation', [
            'messages' => $messages,
            'user' => $user,
            'admin' => $admin,
            'isAdminThread' => $isAdminThread,
            'mentionable' => $mentionable,
        ]);
    }

    // Staff: view messages where they were @mentioned
    public function mentions()
    {
        $mentions = Message::where('mentioned_user_id', Auth::id())
            ->with(['sender', 'receiver'])
            ->latest()
            ->paginate(15);

        return view('messages.mentions', compact('mentions'));
    }

    // Admin: list every rep <-> coordinator conversation for oversight
    public function monitorIndex()
    {
        $pairs = Message::whereNotNull('mentioned_user_id')
            ->orWhereHas('sender.role', fn($q) => $q->where('name', 'staff'))
            ->get()
            ->filter(function ($msg) {
                // Only pairs that don't involve Admin directly
                return $msg->sender?->role?->name !== 'admin' && $msg->receiver?->role?->name !== 'admin';
            })
            ->map(function ($msg) {
                $repId = $msg->sender?->role?->name === 'barangay_user' ? $msg->sender_id : $msg->receiver_id;
                $coordinatorId = $msg->mentioned_user_id ?? ($msg->sender?->role?->name === 'staff' ? $msg->sender_id : null);
                return $repId && $coordinatorId ? $repId . '-' . $coordinatorId : null;
            })
            ->filter()
            ->unique();

        $threads = $pairs->map(function ($pairKey) {
            [$repId, $coordinatorId] = explode('-', $pairKey);
            return [
                'rep' => User::find($repId),
                'coordinator' => User::find($coordinatorId),
            ];
        })->filter(fn($t) => $t['rep'] && $t['coordinator'])->values();

        return view('messages.monitor-index', compact('threads'));
    }

    // Admin: read-only view of one rep <-> coordinator conversation
    public function monitorShow(User $rep, User $coordinator)
    {
        $messages = Message::where(function ($q) use ($rep, $coordinator) {
            $q->where('sender_id', $rep->id)->where('mentioned_user_id', $coordinator->id);
        })->orWhere(function ($q) use ($rep, $coordinator) {
            $q->where('sender_id', $coordinator->id)->where('receiver_id', $rep->id);
        })->orderBy('created_at')->get();

        return view('messages.monitor-show', compact('messages', 'rep', 'coordinator'));
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