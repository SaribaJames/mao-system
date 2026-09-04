@extends('layouts.app')

@section('content')

<div class="mb-6 flex items-center gap-3">
    <a href="{{ route('messages.chat') }}"
       class="text-gray-400 hover:text-gray-600 transition">
        <i class="fa-solid fa-arrow-left"></i>
    </a>
    @if($user->photo)
        <img src="{{ asset('storage/' . $user->photo) }}" class="w-9 h-9 rounded-full object-cover"/>
    @else
        <div class="w-9 h-9 rounded-full bg-primary flex items-center justify-center">
            <span class="text-white text-sm font-bold">{{ strtoupper(substr($user->name, 0, 1)) }}</span>
        </div>
    @endif
    <div>
        <h2 class="text-base font-bold text-gray-800 dark:text-gray-100">{{ $user->name }}</h2>
        <p class="text-xs text-gray-400">{{ $isAdminThread ? 'MAO Admin' : 'Program Coordinator' }}</p>
    </div>
</div>

@if(session('error'))
<div class="bg-red-50 dark:bg-red-900/30 border border-red-200 dark:border-red-800 text-red-600 dark:text-red-300 text-sm rounded px-4 py-2 mb-4">
    {{ session('error') }}
</div>
@endif

<div class="bg-white dark:bg-gray-800 rounded border border-gray-300 dark:border-gray-700 overflow-hidden">
    <div class="h-96 overflow-y-auto p-5 space-y-3" id="messageContainer">
        @forelse($messages as $message)
        @php $isMine = $message->sender_id === Auth::id(); @endphp
        <div class="flex {{ $isMine ? 'justify-end' : 'justify-start' }}">
            <div class="max-w-xs lg:max-w-md">
                @if($message->message)
                <div class="px-4 py-2.5 rounded text-sm border
                    {{ $isMine
                        ? 'bg-primary text-white border-primary-dark'
                        : 'bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-100 border-gray-200 dark:border-gray-600' }}">
                    {{ $message->message }}
                </div>
                @endif

                @if($message->attachment_path)
                <div class="mt-1.5">
                    @if($message->attachment_type === 'image')
                        <a href="{{ $message->attachment_url }}" target="_blank">
                            <img src="{{ $message->attachment_url }}"
                                 class="rounded border border-gray-300 max-w-full max-h-48 object-cover">
                        </a>
                    @else
                        <a href="{{ $message->attachment_url }}" target="_blank"
                           class="flex items-center gap-2 px-3 py-2 rounded border {{ $isMine ? 'bg-primary-dark border-primary-dark text-white' : 'bg-white dark:bg-gray-700 border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-200' }} text-xs hover:opacity-80 transition">
                            <i class="fa-solid {{ $message->attachment_type === 'pdf' ? 'fa-file-pdf' : 'fa-file-lines' }}"></i>
                            <span class="truncate max-w-[160px]">{{ $message->attachment_name }}</span>
                            <i class="fa-solid fa-download ml-auto"></i>
                        </a>
                    @endif
                </div>
                @endif

                <p class="text-xs text-gray-400 mt-1 {{ $isMine ? 'text-right' : 'text-left' }}">
                    {{ $isMine ? 'You' : $message->sender->name }}
                    · {{ $message->created_at->format('M d, h:i A') }}
                </p>
            </div>
        </div>
        @empty
        <div class="text-center text-gray-400 py-8">
            <i class="fa-solid fa-comments text-2xl mb-2"></i>
            <p class="text-sm">No messages yet.</p>
            <p class="text-xs mt-1">Send a message to get started!</p>
        </div>
        @endforelse
    </div>

    {{-- Message Input --}}
    <div class="border-t border-gray-200 dark:border-gray-700 p-4">
        <form method="POST" action="{{ route('messages.send') }}" class="space-y-2" enctype="multipart/form-data" id="chatForm">
            @csrf

            @if($isAdminThread)
                <input type="hidden" name="receiver_id" value="{{ $user->id }}">
                <input type="hidden" name="mentioned_user_id" id="mentionedUserId">
            @else
                {{-- Replying directly within a coordinator thread: message goes to
                     Admin as usual, but is mentioned/routed to this same coordinator
                     so it stays in this thread --}}
                <input type="hidden" name="receiver_id" value="{{ $admin?->id }}">
                <input type="hidden" name="mentioned_user_id" value="{{ $user->id }}">
            @endif

            <div id="filePreview" class="hidden items-center gap-2 bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded px-3 py-1.5 text-xs text-gray-600 dark:text-gray-300 w-fit">
                <i class="fa-solid fa-paperclip"></i>
                <span id="fileName"></span>
                <button type="button" onclick="clearFile()" class="text-red-500 hover:text-red-700 ml-1">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>

            <div class="relative">
                @if($isAdminThread)
                <div id="mentionDropdown" class="hidden absolute bottom-full mb-1 left-0 w-64 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded shadow-lg z-20 max-h-48 overflow-y-auto"></div>
                @endif

                <div class="flex gap-3">
                    <label for="attachmentInput" class="flex-shrink-0 border border-gray-300 dark:border-gray-600 rounded px-3 py-2 text-sm text-gray-500 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-600 cursor-pointer transition">
                        <i class="fa-solid fa-paperclip"></i>
                    </label>
                    <input type="file" name="attachment" id="attachmentInput" class="hidden"
                           accept=".jpg,.jpeg,.png,.gif,.pdf,.doc,.docx,.xls,.xlsx">

                    <input type="text" name="message" id="messageInput"
                           placeholder="{{ $isAdminThread ? 'Type your message... use @ to ask a specific program coordinator' : 'Reply to ' . $user->name . '...' }}"
                           autocomplete="off"
                           class="flex-1 border border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-100 rounded px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary"/>
                    <button type="submit"
                            class="bg-primary hover:bg-primary-dark text-white px-4 py-2 rounded text-sm font-medium transition">
                        <i class="fa-solid fa-paper-plane"></i>
                    </button>
                </div>
            </div>
            @if($isAdminThread)
            <p class="text-xs text-gray-400">Tip: type <span class="font-mono">@</span> followed by a coordinator's name to route your question to them directly.</p>
            @endif
        </form>
    </div>
</div>

<script>
    const container = document.getElementById('messageContainer');
    container.scrollTop = container.scrollHeight;

    const attachmentInput = document.getElementById('attachmentInput');
    const filePreview = document.getElementById('filePreview');
    const fileName = document.getElementById('fileName');

    attachmentInput.addEventListener('change', function () {
        if (this.files.length > 0) {
            fileName.textContent = this.files[0].name;
            filePreview.classList.remove('hidden');
            filePreview.classList.add('flex');
        }
    });

    function clearFile() {
        attachmentInput.value = '';
        filePreview.classList.add('hidden');
        filePreview.classList.remove('flex');
    }

    @if($isAdminThread)
    // @mention autocomplete — only shown on the Admin thread
    const mentionable = @json($mentionable);
    const messageInput = document.getElementById('messageInput');
    const mentionDropdown = document.getElementById('mentionDropdown');
    const mentionedUserIdField = document.getElementById('mentionedUserId');

    function getMentionQuery() {
        const val = messageInput.value;
        const cursor = messageInput.selectionStart;
        const upToCursor = val.slice(0, cursor);
        const atIndex = upToCursor.lastIndexOf('@');
        if (atIndex === -1) return null;
        const afterAt = upToCursor.slice(atIndex + 1);
        if (/\s/.test(afterAt)) return null;
        return { query: afterAt.toLowerCase(), atIndex };
    }

    messageInput.addEventListener('input', function () {
        const mention = getMentionQuery();
        if (!mention) {
            mentionDropdown.classList.add('hidden');
            return;
        }

        const matches = mentionable.filter(u => u.name.toLowerCase().includes(mention.query));
        if (matches.length === 0) {
            mentionDropdown.classList.add('hidden');
            return;
        }

        mentionDropdown.innerHTML = matches.map(u => `
            <button type="button"
                class="w-full text-left px-3 py-2 text-sm text-gray-700 dark:text-gray-200 hover:bg-primary-light dark:hover:bg-gray-600 transition flex items-center gap-2"
                onclick="selectMention(${u.id}, '${u.name.replace(/'/g, "\\'")}', ${mention.atIndex})">
                <i class="fa-solid fa-user-tie text-primary text-xs"></i>
                ${u.name}
            </button>
        `).join('');
        mentionDropdown.classList.remove('hidden');
    });

    function selectMention(id, name, atIndex) {
        const val = messageInput.value;
        const cursor = messageInput.selectionStart;
        const before = val.slice(0, atIndex);
        const after = val.slice(cursor);
        messageInput.value = `${before}@${name} ${after}`;
        mentionedUserIdField.value = id;
        mentionDropdown.classList.add('hidden');
        messageInput.focus();
    }

    document.addEventListener('click', function (e) {
        if (!mentionDropdown.contains(e.target) && e.target !== messageInput) {
            mentionDropdown.classList.add('hidden');
        }
    });

    messageInput.addEventListener('input', function () {
        if (mentionedUserIdField.value && !messageInput.value.includes('@')) {
            mentionedUserIdField.value = '';
        }
    });
    @endif
</script>

@endsection