<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Municipal Agriculture Office — Guinobatan, Albay</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#2D7A2D',
                        'primary-dark': '#1f5c1f',
                        'primary-light': '#F6F8F6',
                        accent: '#D4A017',
                        'accent-dark': '#a97e11',
                        'border-soft': '#D8DFD8',
                    },
                    fontFamily: {
                        sans: ['Inter', 'ui-sans-serif', 'system-ui', 'sans-serif'],
                    }
                }
            }
        }
    </script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', ui-sans-serif, system-ui, sans-serif; }
    </style>
</head>

<body class="bg-primary-light">

    {{-- Top bar --}}
    <header class="bg-gradient-to-r from-primary to-primary-dark border-b-4 border-accent">
        <div class="max-w-6xl mx-auto px-6 py-4 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <img src="{{ asset('images/mao-logo.png') }}" alt="MAO Logo" class="w-10 h-10 rounded-full object-cover bg-white p-0.5 ring-2 ring-accent">
                <div>
                    <p class="text-white font-bold text-sm leading-tight">Municipal Agriculture Office</p>
                    <p class="text-accent text-xs font-semibold uppercase tracking-wide">Guinobatan, Albay</p>
                </div>
            </div>
            <a href="{{ route('login') }}"
               class="bg-accent hover:bg-accent-dark text-primary-dark font-bold text-sm px-5 py-2.5 rounded transition">
                Staff / Barangay Login
            </a>
        </div>
    </header>

    {{-- Hero --}}
    <section class="relative h-[420px] overflow-hidden">
        <img src="{{ asset('images/guinobatan-plaza.jpg') }}" alt="Guinobatan Town Plaza" class="w-full h-full object-cover">
        <div class="absolute inset-0 bg-gradient-to-t from-primary-dark/95 via-primary-dark/60 to-primary-dark/20"></div>
        <div class="absolute inset-0 flex items-center">
            <div class="max-w-6xl mx-auto px-6 w-full">
                <p class="text-accent font-semibold text-sm uppercase tracking-widest mb-3">
                    Republic of the Philippines · Province of Albay
                </p>
                <h1 class="text-4xl md:text-5xl font-extrabold text-white leading-tight max-w-2xl">
                    Digital Farmer Records &amp; Service Management System
                </h1>
                <p class="text-gray-100 text-base mt-4 max-w-xl">
                    Serving the farmers, fisherfolk, and agri-workers of Guinobatan with faster registration,
                    transparent service tracking, and organized agricultural program management.
                </p>
                <a href="{{ route('login') }}"
                   class="inline-block mt-6 bg-accent hover:bg-accent-dark text-primary-dark font-bold text-sm px-6 py-3 rounded transition">
                    Access the System <i class="fa-solid fa-arrow-right ml-1"></i>
                </a>
            </div>
        </div>
    </section>

    {{-- Services --}}
    <section class="max-w-6xl mx-auto px-6 py-16">
        <div class="text-center mb-12">
            <p class="text-primary font-semibold text-sm uppercase tracking-widest mb-2">What We Offer</p>
            <h2 class="text-3xl font-bold text-gray-900">Our Services</h2>
            <p class="text-gray-500 text-sm mt-3 max-w-2xl mx-auto leading-relaxed">
                Managed through this system by authorized barangay representatives and MAO personnel.
            </p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">

            <div class="bg-white rounded-xl border border-border-soft p-6 hover:shadow-md transition">
                <div class="w-12 h-12 rounded-lg bg-primary/10 flex items-center justify-center mb-4">
                    <i class="fa-solid fa-user-plus text-primary text-lg"></i>
                </div>
                <h3 class="font-bold text-gray-900 mb-2">Farmer Registration (RSBSA)</h3>
                <p class="text-sm text-gray-500 mb-3">
                    Official enrollment into the Registry System for Basic Sectors in Agriculture, the government's master list of qualified beneficiaries.
                </p>
                <ul class="text-sm text-gray-600 space-y-1.5">
                    <li class="flex items-start gap-2"><i class="fa-solid fa-check text-primary text-xs mt-1"></i>Covers farmers, farmworkers, fisherfolk, and agri-youth</li>
                    <li class="flex items-start gap-2"><i class="fa-solid fa-check text-primary text-xs mt-1"></i>Captures crop, livestock, and farm parcel details</li>
                    <li class="flex items-start gap-2"><i class="fa-solid fa-check text-primary text-xs mt-1"></i>Reviewed and approved by MAO before official status</li>
                </ul>
            </div>

            <div class="bg-white rounded-xl border border-border-soft p-6 hover:shadow-md transition">
                <div class="w-12 h-12 rounded-lg bg-primary/10 flex items-center justify-center mb-4">
                    <i class="fa-solid fa-seedling text-primary text-lg"></i>
                </div>
                <h3 class="font-bold text-gray-900 mb-2">Agricultural Programs</h3>
                <p class="text-sm text-gray-500 mb-3">
                    Nine active municipal programs, each with a dedicated coordinator and tracked enrollment.
                </p>
                <ul class="text-sm text-gray-600 space-y-1.5">
                    <li class="flex items-start gap-2"><i class="fa-solid fa-check text-primary text-xs mt-1"></i>Rice, Corn, and 4-H Club Programs</li>
                    <li class="flex items-start gap-2"><i class="fa-solid fa-check text-primary text-xs mt-1"></i>HVCDP-OAP-G3HP, NUPAP, and Swine Dispersal</li>
                    <li class="flex items-start gap-2"><i class="fa-solid fa-check text-primary text-xs mt-1"></i>Livestock &amp; Fisheries, RBOs &amp; RIC, FITS Center</li>
                </ul>
            </div>

            <div class="bg-white rounded-xl border border-border-soft p-6 hover:shadow-md transition">
                <div class="w-12 h-12 rounded-lg bg-primary/10 flex items-center justify-center mb-4">
                    <i class="fa-solid fa-boxes-stacked text-primary text-lg"></i>
                </div>
                <h3 class="font-bold text-gray-900 mb-2">Resource Distribution</h3>
                <p class="text-sm text-gray-500 mb-3">
                    Tracked inventory of agricultural supplies released to registered farmers across the municipality.
                </p>
                <ul class="text-sm text-gray-600 space-y-1.5">
                    <li class="flex items-start gap-2"><i class="fa-solid fa-check text-primary text-xs mt-1"></i>Seeds, fertilizer, and pesticides</li>
                    <li class="flex items-start gap-2"><i class="fa-solid fa-check text-primary text-xs mt-1"></i>Farm equipment and tools</li>
                    <li class="flex items-start gap-2"><i class="fa-solid fa-check text-primary text-xs mt-1"></i>Full add/release transaction history per item</li>
                </ul>
            </div>

            <div class="bg-white rounded-xl border border-border-soft p-6 hover:shadow-md transition">
                <div class="w-12 h-12 rounded-lg bg-primary/10 flex items-center justify-center mb-4">
                    <i class="fa-solid fa-file-lines text-primary text-lg"></i>
                </div>
                <h3 class="font-bold text-gray-900 mb-2">Service Requests &amp; Certifications</h3>
                <p class="text-sm text-gray-500 mb-3">
                    Farmers can request assistance through their barangay, tracked from submission to completion.
                </p>
                <ul class="text-sm text-gray-600 space-y-1.5">
                    <li class="flex items-start gap-2"><i class="fa-solid fa-check text-primary text-xs mt-1"></i>Status tracking: pending, approved, completed</li>
                    <li class="flex items-start gap-2"><i class="fa-solid fa-check text-primary text-xs mt-1"></i>Service records tied to individual farmer profiles</li>
                    <li class="flex items-start gap-2"><i class="fa-solid fa-check text-primary text-xs mt-1"></i>Direct messaging with MAO staff for follow-up</li>
                </ul>
            </div>

            <div class="bg-white rounded-xl border border-border-soft p-6 hover:shadow-md transition">
                <div class="w-12 h-12 rounded-lg bg-primary/10 flex items-center justify-center mb-4">
                    <i class="fa-solid fa-file-invoice text-primary text-lg"></i>
                </div>
                <h3 class="font-bold text-gray-900 mb-2">Forms &amp; Documents</h3>
                <p class="text-sm text-gray-500 mb-3">
                    Official government forms filled digitally and printed on the exact original layout.
                </p>
                <ul class="text-sm text-gray-600 space-y-1.5">
                    <li class="flex items-start gap-2"><i class="fa-solid fa-check text-primary text-xs mt-1"></i>PCIC ADSS life &amp; accident insurance application</li>
                    <li class="flex items-start gap-2"><i class="fa-solid fa-check text-primary text-xs mt-1"></i>Livestock Mortality Insurance (LIV-UPI-01)</li>
                    <li class="flex items-start gap-2"><i class="fa-solid fa-check text-primary text-xs mt-1"></i>Printable, official-format PDF output</li>
                </ul>
            </div>

            <div class="bg-white rounded-xl border border-border-soft p-6 hover:shadow-md transition">
                <div class="w-12 h-12 rounded-lg bg-primary/10 flex items-center justify-center mb-4">
                    <i class="fa-solid fa-chart-bar text-primary text-lg"></i>
                </div>
                <h3 class="font-bold text-gray-900 mb-2">Reports &amp; Records</h3>
                <p class="text-sm text-gray-500 mb-3">
                    Consolidated data to support planning, budgeting, and reporting at the municipal level.
                </p>
                <ul class="text-sm text-gray-600 space-y-1.5">
                    <li class="flex items-start gap-2"><i class="fa-solid fa-check text-primary text-xs mt-1"></i>Farmer, request, and service statistics</li>
                    <li class="flex items-start gap-2"><i class="fa-solid fa-check text-primary text-xs mt-1"></i>Narrative and PDF report generation</li>
                    <li class="flex items-start gap-2"><i class="fa-solid fa-check text-primary text-xs mt-1"></i>Program-level activity and budget tracking</li>
                </ul>
            </div>

        </div>
    </section>

    {{-- Program Achievements — real photos posted by coordinators --}}
    @if($achievements->count() > 0)
    <section class="bg-primary-light py-16">
        <div class="max-w-6xl mx-auto px-6">
            <div class="text-center mb-12">
                <p class="text-primary font-semibold text-sm uppercase tracking-widest mb-2">From the Field</p>
                <h2 class="text-3xl font-bold text-gray-900">Program Achievements</h2>
                <p class="text-gray-500 text-sm mt-2 max-w-xl mx-auto">
                    Recent activities and milestones shared by our program coordinators.
                </p>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
                @foreach($achievements as $achievement)
                <div class="rounded-xl overflow-hidden relative h-64 group cursor-pointer"
                     onclick="openLightbox('{{ asset('storage/' . $achievement->photo_path) }}', '{{ addslashes($achievement->program->name) }}', '{{ addslashes($achievement->caption ?: 'Program activity update') }}')">
                  <img src="{{ \Illuminate\Support\Facades\Storage::disk('cloudinary')->url($achievement->photo_path) }}" alt="{{ $achievement->program->name }}"
                         class="w-full h-full object-cover group-hover:scale-105 transition duration-500">
                    <div class="absolute inset-0 bg-gradient-to-t from-primary-dark/95 via-primary-dark/40 to-transparent"></div>
                    <div class="absolute bottom-0 left-0 right-0 p-5">
                        <p class="text-xs font-bold uppercase tracking-widest text-accent mb-1.5">{{ $achievement->program->name }}</p>
                        <p class="text-sm text-gray-100 leading-relaxed">{{ $achievement->caption ?: 'Program activity update' }}</p>
                        <p class="text-xs text-gray-300 mt-1.5">{{ $achievement->created_at->format('M d, Y') }}</p>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </section>
    @endif

    {{-- Access note --}}
    <section class="bg-white border-t border-b border-border-soft py-10">
        <div class="max-w-3xl mx-auto px-6 text-center">
            <i class="fa-solid fa-circle-info text-primary text-2xl mb-3"></i>
            <p class="text-gray-700 text-sm">
                Access to this system is limited to authorized Municipal Agriculture Office personnel and
                designated Barangay Representatives. Farmers do not register their own accounts — please
                coordinate with your Barangay office for registration and service requests.
            </p>
        </div>
    </section>

    {{-- Footer --}}
    <footer class="bg-primary-dark py-8">
        <div class="max-w-6xl mx-auto px-6 text-center">
            <p class="text-white text-sm font-semibold">Municipal Agriculture Office</p>
            <p class="text-gray-300 text-xs mt-1">Municipality of Guinobatan, Province of Albay, Philippines</p>
        </div>
    </footer>


    {{-- Lightbox --}}
    <div id="lightbox" class="hidden fixed inset-0 bg-black/90 z-50 items-center justify-center p-6" onclick="closeLightbox()">
        <button onclick="closeLightbox()" class="absolute top-6 right-6 text-white text-2xl hover:text-accent transition">
            <i class="fa-solid fa-xmark"></i>
        </button>
        <div class="max-w-4xl w-full" onclick="event.stopPropagation()">
            <img id="lightboxImg" src="" class="w-full max-h-[75vh] object-contain rounded-lg">
            <div class="mt-4 text-center">
                <p id="lightboxProgram" class="text-accent font-bold text-sm uppercase tracking-widest mb-1"></p>
                <p id="lightboxCaption" class="text-white text-base"></p>
            </div>
        </div>
    </div>

    <script>
        function openLightbox(src, program, caption) {
            document.getElementById('lightboxImg').src = src;
            document.getElementById('lightboxProgram').textContent = program;
            document.getElementById('lightboxCaption').textContent = caption;
            document.getElementById('lightbox').classList.remove('hidden');
            document.getElementById('lightbox').classList.add('flex');
        }
        function closeLightbox() {
            document.getElementById('lightbox').classList.add('hidden');
            document.getElementById('lightbox').classList.remove('flex');
        }
    </script>

</body>
</html>