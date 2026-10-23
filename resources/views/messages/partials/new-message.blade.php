{{-- "New Message" picker: lists every user this account can message, so
     nobody has to wait for the other person to write first. --}}
<button type="button" onclick="document.getElementById('newMessageModal').classList.remove('hidden'); document.getElementById('contactSearch').focus();"
        class="bg-primary hover:bg-primary-dark text-white text-sm font-semibold px-4 py-2 rounded-md flex items-center gap-2 transition">
    <i class="fa-solid fa-pen-to-square"></i> New Message
</button>

<div id="newMessageModal" class="hidden fixed inset-0 bg-black bg-opacity-40 flex items-center justify-center z-50 p-4"
     onclick="if (event.target === this) this.classList.add('hidden')">
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-xl w-full max-w-md max-h-[80vh] flex flex-col">
        <div class="flex items-center justify-between px-5 py-4 border-b border-border-soft dark:border-gray-700">
            <h3 class="font-semibold text-gray-800 dark:text-gray-100">New Message</h3>
            <button type="button" onclick="document.getElementById('newMessageModal').classList.add('hidden')"
                    class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>

        <div class="px-5 py-3 border-b border-border-soft dark:border-gray-700">
            <div class="relative">
                <i class="fa-solid fa-magnifying-glass absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm"></i>
                <input type="text" id="contactSearch" placeholder="Search by name, role or barangay..." autocomplete="off"
                       class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-gray-100 rounded-lg pl-10 pr-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary">
            </div>
        </div>

        <div class="overflow-y-auto flex-1">
            @forelse($contacts as $contact)
                <a href="{{ $contact['url'] }}"
                   data-contact="{{ strtolower($contact['user']->name . ' ' . $contact['label']) }}"
                   class="flex items-center gap-3 px-5 py-3 hover:bg-gray-50 dark:hover:bg-gray-700 transition border-b border-gray-50 dark:border-gray-700 last:border-0">
                    @if($contact['user']->photo)
                        <img src="{{ asset('storage/' . $contact['user']->photo) }}" class="w-9 h-9 rounded-full object-cover flex-shrink-0"/>
                    @else
                        <div class="w-9 h-9 rounded-full bg-primary flex items-center justify-center flex-shrink-0">
                            <span class="text-white text-sm font-bold">{{ strtoupper(substr($contact['user']->name, 0, 1)) }}</span>
                        </div>
                    @endif
                    <div class="min-w-0">
                        <p class="text-sm font-semibold text-gray-800 dark:text-gray-100 truncate">{{ $contact['user']->name }}</p>
                        <p class="text-xs text-gray-400 truncate">{{ $contact['label'] }}</p>
                    </div>
                </a>
            @empty
                <p class="px-5 py-8 text-center text-sm text-gray-400">No other users to message yet.</p>
            @endforelse
            <p id="contactNoMatch" class="hidden px-5 py-8 text-center text-sm text-gray-400">No user matches your search.</p>
        </div>
    </div>
</div>

<script>
    (function () {
        const input = document.getElementById('contactSearch');
        if (!input) return;
        input.addEventListener('input', function () {
            const query = this.value.toLowerCase().trim();
            let shown = 0;
            document.querySelectorAll('[data-contact]').forEach(function (row) {
                const match = row.dataset.contact.includes(query);
                row.style.display = match ? '' : 'none';
                if (match) shown++;
            });
            document.getElementById('contactNoMatch').classList.toggle('hidden', shown > 0);
        });
        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape') document.getElementById('newMessageModal').classList.add('hidden');
        });
    })();
</script>
