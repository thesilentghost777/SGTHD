<br>
<div class="flex justify-end gap-4">
    <button
        onclick="window.history.back()"
        class="bg-black text-white hover:bg-gray-800 focus:outline-none focus:ring-2 focus:ring-gray-300 rounded-lg px-6 py-3 flex items-center gap-2 transition-all duration-200"
    >
        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="m12 19-7-7 7-7"/>
            <path d="M19 12H5"/>
        </svg>
        Retour
    </button>

    <a
        href="{{ route('dashboard') }}"
        class="bg-white text-black hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-gray-300 rounded-lg px-6 py-3 flex items-center gap-2 transition-all duration-200 border border-gray-200"
    >
        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <rect width="7" height="9" x="3" y="3" rx="1"/>
            <rect width="7" height="5" x="14" y="3" rx="1"/>
            <rect width="7" height="9" x="14" y="12" rx="1"/>
            <rect width="7" height="5" x="3" y="16" rx="1"/>
        </svg>
        Dashboard
    </a>
</div>
