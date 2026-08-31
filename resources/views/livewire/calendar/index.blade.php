<?php

use Livewire\Component;

new class extends Component
{
    //
};
?>

<div class="p-6">
    <!-- Header Kalender -->
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Kalender Agenda & Deals</h1>
            <p class="text-xs text-gray-500 dark:text-gray-400">Pantau tenggat waktu tugas dan target closing transaksi tim Anda.</p>
        </div>
        <div class="flex items-center gap-2">
            <button wire:click="previousMonth" class="p-2 text-gray-600 dark:text-gray-300 bg-gray-100 dark:bg-zinc-800 rounded-lg hover:bg-gray-200 dark:hover:bg-zinc-700">
                &larr; Prev
            </button>
            <button wire:click="goToToday" class="px-3 py-2 text-xs font-semibold text-gray-700 dark:text-gray-200 bg-gray-100 dark:bg-zinc-800 rounded-lg hover:bg-gray-200 dark:hover:bg-zinc-700">
                Hari Ini
            </button>
            <span class="px-4 py-2 font-bold text-gray-800 dark:text-gray-100 text-sm">
                {{ $monthName }}
            </span>
            <button wire:click="nextMonth" class="p-2 text-gray-600 dark:text-gray-300 bg-gray-100 dark:bg-zinc-800 rounded-lg hover:bg-gray-200 dark:hover:bg-zinc-700">
                Next &rarr;
            </button>
        </div>
    </div>

    <!-- Header Nama Hari -->
    <div class="grid grid-cols-7 gap-px bg-gray-200 dark:bg-zinc-700 rounded-t-xl overflow-hidden text-center text-xs font-semibold text-gray-600 dark:text-gray-300 py-2">
        <div>Minggu</div>
        <div>Senin</div>
        <div>Selasa</div>
        <div>Rabu</div>
        <div>Kamis</div>
        <div>Jumat</div>
        <div>Sabtu</div>
    </div>

    <!-- Grid Tanggal -->
    <div class="grid grid-cols-7 gap-px bg-gray-200 dark:bg-zinc-700 rounded-b-xl overflow-hidden">
        {{-- Slot Kosong Sebelum Hari Pertama --}}
        @for ($i = 0; $i < $startingDayOfWeek; $i++)
            <div class="bg-gray-50/50 dark:bg-zinc-900/50 min-h-[120px]"></div>
        @endfor

        {{-- Loop Hari dalam Bulan --}}
        @for ($day = 1; $day <= $daysInMonth; $day++)
            @php
                $dateString = sprintf('%04d-%02d-%02d', $currentYear, $currentMonth, $day);
                $isToday = $dateString === date('Y-m-d');
                $dayEvents = $eventsByDate[$dateString] ?? [];
            @endphp
            <div class="bg-white dark:bg-zinc-800 min-h-[120px] p-1.5 flex flex-col justify-start border-t border-gray-100 dark:border-zinc-700/50">
                <div class="flex justify-between items-center mb-1">
                    <span class="text-xs font-bold rounded-full w-6 h-6 flex items-center justify-center {{ $isToday ? 'bg-indigo-600 text-white' : 'text-gray-700 dark:text-gray-300' }}">
                        {{ $day }}
                    </span>
                    @if(count($dayEvents) > 0)
                        <span class="text-[10px] bg-gray-100 dark:bg-zinc-700 text-gray-500 px-1.5 py-0.5 rounded-full font-medium">
                            {{ count($dayEvents) }} event
                        </span>
                    @endif
                </div>

                <!-- Event Badges -->
                <div class="space-y-1 overflow-y-auto max-h-[85px] text-[11px]">
                    @foreach ($dayEvents as $event)
                        @if ($event['type'] === 'task')
                            <div class="px-1.5 py-0.5 rounded truncate border {{ $event['is_completed'] ? 'bg-emerald-50 text-emerald-700 border-emerald-200 dark:bg-emerald-950/40 dark:text-emerald-300' : 'bg-amber-50 text-amber-700 border-amber-200 dark:bg-amber-950/40 dark:text-amber-300' }}">
                                📌 {{ $event['title'] }}
                            </div>
                        @elseif ($event['type'] === 'deal')
                            <div class="px-1.5 py-0.5 rounded truncate bg-indigo-50 text-indigo-700 border border-indigo-200 dark:bg-indigo-950/40 dark:text-indigo-300 font-semibold">
                                💰 {{ $event['title'] }}
                            </div>
                        @endif
                    @endforeach
                </div>
            </div>
        @endfor
    </div>
</div>