<?php

use Livewire\Component;

new class extends Component
{
    //
};
?>

<div x-data="{ 
        open: @entangle('isOpen'),
        focusIndex: 0,
        totalItems: 0,
        navigate(dir) {
            if (this.totalItems === 0) return;
            if (dir === 'down') {
                this.focusIndex = (this.focusIndex + 1) % this.totalItems;
            } else {
                this.focusIndex = (this.focusIndex - 1 + this.totalItems) % this.totalItems;
            }
        },
        selectCurrent() {
            const activeEl = $refs.resultsContainer?.querySelector(`[data-index='${this.focusIndex}']`);
            if (activeEl) activeEl.click();
        }
     }" 
     x-on:keydown.window.prevent.cmd.k="open = true; $wire.openModal()"
     x-on:keydown.window.prevent.ctrl.k="open = true; $wire.openModal()"
     x-on:keydown.escape.window="open = false; $wire.closeModal()">

    <!-- Responsive Trigger Button -->
    <button @click="open = true; $wire.openModal()" 
            class="w-full flex items-center justify-between gap-2 px-2.5 sm:px-3 py-1.5 sm:py-2 text-sm text-gray-500 bg-gray-100 dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 hover:bg-gray-200 transition">
        <div class="flex items-center gap-2 min-w-0">
            <svg class="w-4 h-4 text-gray-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
            </svg>
            <span class="truncate text-xs sm:text-sm">Cari kamar, gedung...</span>
        </div>
        <kbd class="hidden md:inline-block shrink-0 px-1.5 py-0.5 text-[10px] text-gray-400 bg-white dark:bg-gray-900 border border-gray-300 dark:border-gray-600 rounded">Ctrl K</kbd>
    </button>

    <!-- Modal Command Palette Overlay -->
    <div x-show="open" 
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-50 overflow-y-auto p-2 sm:p-4 md:p-10 bg-gray-900/60 backdrop-blur-sm flex justify-center items-start pt-10 sm:pt-16"
         style="display: none;">

        <div @click.away="open = false; $wire.closeModal()" 
             @keydown.arrow-down.prevent="navigate('down')"
             @keydown.arrow-up.prevent="navigate('up')"
             @keydown.enter.prevent="selectCurrent()"
             class="w-full max-w-lg md:max-w-2xl bg-white dark:bg-gray-900 rounded-xl shadow-2xl border border-gray-200 dark:border-gray-800 overflow-hidden transform transition-all mx-auto">
            
            <!-- Search Input Header -->
            <div class="relative flex items-center px-3 sm:px-4 border-b border-gray-200 dark:border-gray-800">
                <svg class="w-4 sm:w-5 h-4 sm:h-5 text-gray-400 mr-2 sm:mr-3 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
                <input wire:model.live.debounce.250ms="search" 
                       type="text" 
                       class="w-full py-3 sm:py-4 text-gray-800 dark:text-gray-100 bg-transparent border-0 focus:ring-0 text-sm sm:text-base placeholder-gray-400"
                       placeholder="Ketik untuk mencari..."
                       x-ref="searchInput"
                       x-init="$watch('open', value => { if(value) { focusIndex = 0; setTimeout(() => $refs.searchInput.focus(), 100); } })">
                <button @click="open = false; $wire.closeModal()" class="text-xs text-gray-400 hover:text-gray-600 px-2 py-1 rounded bg-gray-100 dark:bg-gray-800 shrink-0">ESC</button>
            </div>

            <!-- Search Results Container -->
            <div x-ref="resultsContainer" class="max-h-[60vh] sm:max-h-96 overflow-y-auto p-3 sm:p-4 space-y-4">
                @if(strlen(trim($search)) < 2)
                    <div class="text-center py-6 sm:py-8 text-gray-400 text-xs sm:text-sm">
                        Ketik minimal 2 karakter untuk memulai pencarian.
                    </div>
                @else
                    @php
                        $currentIndex = 0;
                        $hasResults = $results['rooms']->count() > 0 || $results['properties']->count() > 0 || $results['scenes']->count() > 0;
                        $totalCount = $results['rooms']->count() + $results['properties']->count() + $results['scenes']->count();
                    @endphp

                    <div x-init="totalItems = {{ $totalCount }}"></div>

                    @if(!$hasResults)
                        <div class="text-center py-6 sm:py-8 text-gray-500 text-xs sm:text-sm">
                            Tidak ditemukan hasil untuk "<span class="font-semibold text-gray-700 dark:text-gray-300">{{ $search }}</span>"
                        </div>
                    @else
                        <!-- Group 1: Kamar & Unit -->
                        @if($results['rooms']->count() > 0)
                            <div>
                                <h4 class="text-[10px] sm:text-xs font-semibold text-gray-400 uppercase tracking-wider px-2 mb-1.5">Kamar & Unit</h4>
                                <div class="space-y-1">
                                    @foreach($results['rooms'] as $room)
                                        @php $itemIdx = $currentIndex++; @endphp
                                        <a href="{{ route('rooms.show', $room->id) }}" wire:navigate 
                                           data-index="{{ $itemIdx }}"
                                           :class="{ 'bg-indigo-50 dark:bg-gray-800 text-indigo-600': focusIndex === {{ $itemIdx }} }"
                                           @mouseenter="focusIndex = {{ $itemIdx }}"
                                           class="flex items-center justify-between p-2 sm:p-2.5 rounded-lg hover:bg-indigo-50 dark:hover:bg-gray-800 transition group">
                                            <div class="flex items-center gap-2 sm:gap-3 min-w-0">
                                                <span class="p-1.5 sm:p-2 bg-indigo-100 dark:bg-indigo-950 text-indigo-600 rounded-md text-xs sm:text-sm shrink-0">🏠</span>
                                                <div class="min-w-0">
                                                    <p class="text-xs sm:text-sm font-medium text-gray-800 dark:text-gray-200 truncate">
                                                        Kamar {!! preg_replace('/('.preg_quote($search, '/').')/i', '<mark class="bg-yellow-200 dark:bg-yellow-800 px-0.5 rounded">$1</mark>', e($room->room_number)) !!} 
                                                        ({!! preg_replace('/('.preg_quote($search, '/').')/i', '<mark class="bg-yellow-200 dark:bg-yellow-800 px-0.5 rounded">$1</mark>', e($room->type)) !!})
                                                    </p>
                                                    <p class="text-[10px] sm:text-xs text-gray-400 truncate">{{ $room->property->name ?? 'Properti N/A' }}</p>
                                                </div>
                                            </div>
                                            <span class="text-[10px] sm:text-xs font-semibold text-indigo-600 shrink-0 ml-2">Rp {{ number_format($room->price_per_month, 0, ',', '.') }}/bln</span>
                                        </a>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        <!-- Group 2: Properti -->
                        @if($results['properties']->count() > 0)
                            <div>
                                <h4 class="text-[10px] sm:text-xs font-semibold text-gray-400 uppercase tracking-wider px-2 mb-1.5">Properti / Gedung</h4>
                                <div class="space-y-1">
                                    @foreach($results['properties'] as $property)
                                        @php $itemIdx = $currentIndex++; @endphp
                                        <a href="{{ route('rooms.index', ['propertyFilter' => $property->id]) }}" wire:navigate 
                                           data-index="{{ $itemIdx }}"
                                           :class="{ 'bg-emerald-50 dark:bg-gray-800 text-emerald-600': focusIndex === {{ $itemIdx }} }"
                                           @mouseenter="focusIndex = {{ $itemIdx }}"
                                           class="flex items-center gap-2 sm:gap-3 p-2 sm:p-2.5 rounded-lg hover:bg-emerald-50 dark:hover:bg-gray-800 transition group">
                                            <span class="p-1.5 sm:p-2 bg-emerald-100 dark:bg-emerald-950 text-emerald-600 rounded-md text-xs sm:text-sm shrink-0">🏢</span>
                                            <div class="min-w-0">
                                                <p class="text-xs sm:text-sm font-medium text-gray-800 dark:text-gray-200 truncate">
                                                    {!! preg_replace('/('.preg_quote($search, '/').')/i', '<mark class="bg-yellow-200 dark:bg-yellow-800 px-0.5 rounded">$1</mark>', e($property->name)) !!}
                                                </p>
                                                <p class="text-[10px] sm:text-xs text-gray-400 truncate">{{ $property->address ?? 'Tidak ada alamat' }}</p>
                                            </div>
                                        </a>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        <!-- Group 3: Scene 360° -->
                        @if($results['scenes']->count() > 0)
                            <div>
                                <h4 class="text-[10px] sm:text-xs font-semibold text-gray-400 uppercase tracking-wider px-2 mb-1.5">Virtual Tour Scenes 360°</h4>
                                <div class="space-y-1">
                                    @foreach($results['scenes'] as $scene)
                                        @php $itemIdx = $currentIndex++; @endphp
                                        <a href="{{ route('rooms.show', $scene->room_id) }}" wire:navigate 
                                           data-index="{{ $itemIdx }}"
                                           :class="{ 'bg-purple-50 dark:bg-gray-800 text-purple-600': focusIndex === {{ $itemIdx }} }"
                                           @mouseenter="focusIndex = {{ $itemIdx }}"
                                           class="flex items-center gap-2 sm:gap-3 p-2 sm:p-2.5 rounded-lg hover:bg-purple-50 dark:hover:bg-gray-800 transition group">
                                            <span class="p-1.5 sm:p-2 bg-purple-100 dark:bg-purple-950 text-purple-600 rounded-md text-xs sm:text-sm shrink-0">🌐</span>
                                            <div class="min-w-0">
                                                <p class="text-xs sm:text-sm font-medium text-gray-800 dark:text-gray-200 truncate">
                                                    Scene: {!! preg_replace('/('.preg_quote($search, '/').')/i', '<mark class="bg-yellow-200 dark:bg-yellow-800 px-0.5 rounded">$1</mark>', e($scene->title)) !!}
                                                </p>
                                                <p class="text-[10px] sm:text-xs text-gray-400 truncate">Kamar {{ $scene->room->room_number ?? '' }}</p>
                                            </div>
                                        </a>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    @endif
                @endif
            </div>

            <!-- Footer Navigation Hints -->
            <div class="px-3 sm:px-4 py-2 bg-gray-50 dark:bg-gray-800/50 border-t border-gray-200 dark:border-gray-800 flex items-center justify-between text-[10px] sm:text-xs text-gray-400">
                <div class="flex items-center gap-1.5">
                    <kbd class="px-1 py-0.5 bg-white dark:bg-gray-900 border border-gray-300 dark:border-gray-700 rounded shadow-sm">↑</kbd>
                    <kbd class="px-1 py-0.5 bg-white dark:bg-gray-900 border border-gray-300 dark:border-gray-700 rounded shadow-sm">↓</kbd>
                    <span>Navigasi</span>
                </div>
                <div class="flex items-center gap-1.5">
                    <kbd class="px-1 py-0.5 bg-white dark:bg-gray-900 border border-gray-300 dark:border-gray-700 rounded shadow-sm">Enter</kbd>
                    <span>Pilih</span>
                </div>
            </div>
        </div>
    </div>
</div>