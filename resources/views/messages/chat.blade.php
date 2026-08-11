@extends('layouts.app')

@section('content')

<div class="mb-6 flex items-center gap-3">
    <a href="{{ route('dashboard') }}"
       class="text-gray-400 hover:text-gray-600 transition">
        <i class="fa-solid fa-arrow-left"></i>
    </a>
    <div>
        <h2 class="text-base font-bold text-gray-800">MAO Support</h2>
        <p class="text-xs text-gray-400">Municipal Agriculture Office — Guinobatan</p>
    </div>
</div>

@if(session('error'))
<div class="bg-red-50 border border-red-200 text-red-600 text-sm rounded px-4 py-2 mb-4">
    {{ session('error') }}
</div>
@endif

{{-- Messages --}}
<div class="bg-white rounded border border-gray-300 overflow-hidden">
    <div class="h-96 overflow-y-auto p-5 space-y-3" id="messageContainer">
        @forelse($messages as $message)
        @php $isMine = $message->sender_id === Auth::id(); @endphp
        <div class="flex {{ $isMine ? 'justify-end' : 'justify-start' }}">
            <div class="max-w-xs lg:max-w-md">
                @if($message->message)
                <div class="px-4 py-2.5 rounded text-sm border
                    {{ $isMine
                        ? 'bg-primary text-white border-primary-dark'
                        : 'bg-gray-100 text-gray-800 border-gray-200' }}">
                    {{ $message->message }}
                </div>
                @endif

                @if($message->attachment_path)
                <div class="mt-1.5">
                    @if($message->attachment_type === 'image')
                        <a href="{{ asset('storage/' . $message->attachment_path) }}" target="_blank">
                            <img src="{{ asset('storage/' . $message->attachment_path) }}"
                                 class="rounded border border-gray-300 max-w-full max-h-48 object-cover">
                        </a>
                    @else
                        <a href="{{ asset('storage/' . $message->attachment_path) }}" target="_blank"
                           class="flex items-center gap-2 px-3 py-2 rounded border {{ $isMine ? 'bg-primary-dark border-primary-dark text-white' : 'bg-white border-gray-300 text-gray-700' }} text-xs hover:opacity-80 transition">
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
            <p class="text-xs mt-1">Send a message to MAO staff for assistance!</p>
        </div>
        @endforelse
    </div>

    {{-- Message Input --}}
    <div class="border-t border-gray-200 p-4">
        <form method="POST" action="{{ route('messages.send') }}" class="space-y-2" enctype="multipart/form-data" id="chatForm">
            @csrf
            <input type="hidden" name="receiver_id" value="{{ $admin->id }}">

            <div id="filePreview" class="hidden items-center gap-2 bg-gray-50 border border-gray-200 rounded px-3 py-1.5 text-xs text-gray-600 w-fit">
                <i class="fa-solid fa-paperclip"></i>
                <span id="fileName"></span>
                <button type="button" onclick="clearFile()" class="text-red-500 hover:text-red-700 ml-1">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>

            <div class="flex gap-3">
                <label for="attachmentInput" class="flex-shrink-0 border border-gray-300 rounded px-3 py-2 text-sm text-gray-500 hover:bg-gray-50 cursor-pointer transition">
                    <i class="fa-solid fa-paperclip"></i>
                </label>
                <input type="file" name="attachment" id="attachmentInput" class="hidden"
                       accept=".jpg,.jpeg,.png,.gif,.pdf,.doc,.docx,.xls,.xlsx">

                <input type="text" name="message"
                       placeholder="Type your message to MAO..."
                       autocomplete="off"
                       class="flex-1 border border-gray-300 rounded px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary"/>
                <button type="submit"
                        class="bg-primary hover:bg-primary-dark text-white px-4 py-2 rounded text-sm font-medium transition">
                    <i class="fa-solid fa-paper-plane"></i>
                </button>
            </div>
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
</script>

@endsection