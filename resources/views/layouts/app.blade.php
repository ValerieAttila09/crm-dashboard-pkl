<x-layouts::app.sidebar :title="$title ?? null">
    @include('partials.header')
    {{-- <livewire:notification-bell /> --}}
    <!-- Global Livewire Loading Indicator -->
    <div wire:loading.delay.longer class="fixed inset-0 z-50 flex items-center justify-center bg-black/30 backdrop-blur-[2px] transition-all">
        <div class="bg-white dark:bg-zinc-800 px-5 py-3 rounded-2xl shadow-2xl border border-gray-100 dark:border-zinc-700 flex items-center gap-3">
            <!-- SVG Spinner -->
            <svg class="animate-spin h-5 w-5 text-indigo-600 dark:text-indigo-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            <span class="text-xs font-bold text-gray-700 dark:text-gray-200">Memproses data...</span>
        </div>
    </div>
    <flux:main>
        {{ $slot }}
    </flux:main>
</x-layouts::app.sidebar>
