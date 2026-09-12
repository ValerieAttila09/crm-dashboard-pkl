<?php

use Livewire\Component;

new class extends Component
{
    //
};
?>

<div class="p-6 bg-white dark:bg-zinc-900 min-h-screen">
    
    <!-- Toast Flash Message -->
    @if (session()->has('message'))
        <div class="mb-4 p-3 bg-emerald-100 border border-emerald-300 text-emerald-800 rounded-lg text-xs font-semibold flex items-center justify-between">
            <span>{{ session('message') }}</span>
        </div>
    @endif

    <!-- 1. Header & Tab View Selector -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">My Calendar</h1>
            <div class="flex items-center gap-6 mt-2 border-b border-gray-200 dark:border-zinc-700 text-sm">
                <button wire:click="setViewMode('today')" class="pb-2 font-medium transition {{ $viewMode === 'today' ? 'text-indigo-600 border-b-2 border-indigo-600 dark:text-indigo-400' : 'text-gray-500 hover:text-gray-700' }}">
                    Today
                </button>
                <button wire:click="setViewMode('week')" class="pb-2 font-medium transition {{ $viewMode === 'week' ? 'text-indigo-600 border-b-2 border-indigo-600 dark:text-indigo-400' : 'text-gray-500 hover:text-gray-700' }}">
                    This Week
                </button>
                <button wire:click="setViewMode('month')" class="pb-2 font-medium transition {{ $viewMode === 'month' ? 'text-indigo-600 border-b-2 border-indigo-600 dark:text-indigo-400' : 'text-gray-500 hover:text-gray-700' }}">
                    This Month
                </button>
            </div>
        </div>

        <div class="flex items-center gap-3">
            <span class="text-xs text-gray-500 bg-white dark:bg-zinc-800 px-3 py-2 rounded-lg border border-gray-200 dark:border-zinc-700 shadow-sm font-semibold">
                {{ count($schedules) }} Schedule
            </span>
            <button wire:click="openCreateModal" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg text-xs font-semibold shadow-sm transition">
                + Create Schedule
            </button>
        </div>
    </div>

    <!-- 2. Sub-Header Navigation Bar -->
    <div class="flex items-center justify-between mb-4 bg-white dark:bg-zinc-800 p-3 rounded-xl border border-gray-200 dark:border-zinc-700 shadow-sm">
        <div class="flex items-center gap-3">
            <input type="date" wire:model.live="selectedDate" class="text-xs font-semibold text-gray-700 dark:text-gray-200 bg-gray-100 dark:bg-zinc-700 border-0 rounded-lg py-1.5 px-3">
            <button wire:click="goToToday" class="px-3 py-1.5 text-xs font-semibold bg-gray-100 dark:bg-zinc-700 hover:bg-gray-200 text-gray-700 dark:text-gray-200 rounded-lg transition">
                Today
            </button>
            <span class="text-sm font-bold text-gray-800 dark:text-gray-100">
                {{ $formattedHeader }}
            </span>
        </div>
    </div>

    <!-- 3. MODE TAMPILAN: TODAY (RESOURCE TIMELINE VIEW) -->
    @if($viewMode === 'today')
        <div class="bg-white dark:bg-zinc-800 rounded-2xl border border-gray-200 dark:border-zinc-700 shadow-sm overflow-x-auto relative">
            <div class="grid grid-cols-5 min-w-[800px] border-b border-gray-200 dark:border-zinc-700 bg-gray-50 dark:bg-zinc-900">
                <div class="p-3 text-xs font-bold text-gray-400 border-r border-gray-200 dark:border-zinc-700 text-center">WAKTU</div>
                @foreach($properties->take(4) as $property)
                    <div class="p-3 border-r border-gray-200 dark:border-zinc-700 flex items-center gap-2.5">
                        <div class="w-8 h-8 rounded-full bg-indigo-100 dark:bg-indigo-950 text-indigo-600 flex items-center justify-center font-bold text-xs shrink-0">🏢</div>
                        <div class="min-w-0">
                            <h4 class="text-xs font-bold text-gray-800 dark:text-gray-200 truncate">{{ $property->name }}</h4>
                            <p class="text-[10px] text-gray-400 truncate">{{ $property->rooms->count() }} Kamar</p>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="divide-y divide-gray-100 dark:divide-zinc-700 min-w-[800px]">
                @foreach($timeSlots as $slot)
                    <div class="grid grid-cols-5 min-h-[90px]">
                        <div class="p-3 text-xs font-semibold text-gray-400 border-r border-gray-200 dark:border-zinc-700 flex items-start justify-center">
                            {{ $slot }}
                        </div>
                        @foreach($properties->take(4) as $property)
                            @php
                                $matchedSchedules = $schedules->filter(function($s) use ($property, $slot) {
                                    return $s->property_id == $property->id && $s->start_time->format('H:00') === $slot;
                                });
                            @endphp
                            <div class="border-r border-gray-200 dark:border-zinc-700 p-2 space-y-1 hover:bg-gray-50/50 dark:hover:bg-zinc-700/20 transition">
                                @foreach($matchedSchedules as $sch)
                                    <div class="p-2 rounded-lg border text-xs shadow-sm transition
                                        {{ $sch->type === 'maintenance' ? 'bg-amber-50 border-amber-300 text-amber-900 dark:bg-amber-950/40 dark:text-amber-200' : '' }}
                                        {{ $sch->type === 'survey' ? 'bg-indigo-50 border-indigo-300 text-indigo-900 dark:bg-indigo-950/40 dark:text-indigo-200' : '' }}
                                        {{ $sch->type === 'lease_due' ? 'bg-emerald-50 border-emerald-300 text-emerald-900 dark:bg-emerald-950/40 dark:text-emerald-200' : '' }}">
                                        <div class="flex items-center justify-between">
                                            <span class="font-bold truncate">{{ $sch->title }}</span>
                                            <button wire:click="updateStatus({{ $sch->id }}, '{{ $sch->status === 'done' ? 'upcoming' : 'done' }}')" wire:loading.attr="disabled" wire:target="updateStatus({{ $sch->id }}, '{{ $sch->status === 'done' ? 'upcoming' : 'done' }}')"
                                                    class="text-[9px] px-1.5 py-0.5 rounded font-bold uppercase {{ $sch->status === 'done' ? 'bg-emerald-200 text-emerald-800' : 'bg-gray-200 text-gray-700' }}">
                                                {{ $sch->status }}
                                            </button>
                                        </div>
                                        <p class="text-[10px] opacity-75 mt-0.5">{{ $sch->start_time->format('H:i') }} - {{ $sch->end_time->format('H:i') }}</p>
                                    </div>
                                @endforeach
                            </div>
                        @endforeach
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    <!-- 4. MODE TAMPILAN: THIS WEEK (7 DAYS GRID) -->
    @if($viewMode === 'week')
        <div class="bg-white dark:bg-zinc-800 rounded-2xl border border-gray-200 dark:border-zinc-700 shadow-sm overflow-hidden">
            <div class="grid grid-cols-7 border-b border-gray-200 dark:border-zinc-700 bg-gray-50 dark:bg-zinc-900 text-center text-xs font-bold text-gray-600 dark:text-gray-300 py-3">
                @foreach($weekDays as $day)
                    <div class="{{ $day->isToday() ? 'text-indigo-600 dark:text-indigo-400 font-extrabold' : '' }}">
                        {{ $day->translatedFormat('D') }}
                        <span class="block text-sm font-black">{{ $day->format('d') }}</span>
                    </div>
                @endforeach
            </div>
            <div class="grid grid-cols-7 min-h-[400px] divide-x divide-gray-200 dark:divide-zinc-700">
                @foreach($weekDays as $day)
                    @php
                        $daySchedules = $schedules->filter(fn($s) => $s->start_time->format('Y-m-d') === $day->format('Y-m-d'));
                    @endphp
                    <div class="p-2 space-y-1.5 bg-white dark:bg-zinc-800">
                        @foreach($daySchedules as $sch)
                            <div class="p-2 rounded-lg text-[11px] font-semibold border bg-indigo-50 border-indigo-200 text-indigo-900 dark:bg-indigo-950/40 dark:text-indigo-200">
                                <p class="truncate">{{ $sch->title }}</p>
                                <span class="text-[9px] opacity-70">{{ $sch->start_time->format('H:i') }}</span>
                            </div>
                        @endforeach
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    <!-- 5. MODE TAMPILAN: THIS MONTH (MONTHLY CALENDAR GRID) -->
    @if($viewMode === 'month')
        <div class="bg-white dark:bg-zinc-800 rounded-2xl border border-gray-200 dark:border-zinc-700 shadow-sm p-4">
            <div class="grid grid-cols-7 gap-1 text-center text-xs font-bold text-gray-500 mb-2">
                <div>Sen</div><div>Sel</div><div>Rab</div><div>Kam</div><div>Jum</div><div>Sab</div><div>Ming</div>
            </div>
            <div class="grid grid-cols-7 gap-1">
                @php
                    $startOfMonth = \Carbon\Carbon::parse($selectedDate)->startOfMonth();
                    $daysInMonth = $startOfMonth->daysInMonth;
                @endphp
                @for($d = 1; $d <= $daysInMonth; $d++)
                    @php
                        $dateStr = $startOfMonth->copy()->day($d)->format('Y-m-d');
                        $daySchedules = $schedules->filter(fn($s) => $s->start_time->format('Y-m-d') === $dateStr);
                    @endphp
                    <div class="min-h-[90px] p-1.5 border border-gray-100 dark:border-zinc-700 rounded-xl bg-gray-50/50 dark:bg-zinc-900/40 flex flex-col justify-between">
                        <span class="text-xs font-bold text-gray-700 dark:text-gray-300">{{ $d }}</span>
                        @if($daySchedules->count() > 0)
                            <div class="bg-indigo-600 text-white text-[10px] font-bold px-1.5 py-0.5 rounded-md text-center truncate">
                                {{ $daySchedules->count() }} Schedule
                            </div>
                        @endif
                    </div>
                @endfor
            </div>
        </div>
    @endif

    <!-- MODAL FORM CREATE SCHEDULE -->
    @if($isModalOpen)
        <div class="fixed inset-0 z-50 overflow-y-auto bg-gray-900/60 backdrop-blur-sm flex justify-center items-center p-4">
            <div class="bg-white dark:bg-zinc-900 w-full max-w-md rounded-2xl p-6 shadow-2xl border border-gray-200 dark:border-zinc-800">
                <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4">Tambah Jadwal Baru</h3>
                
                <form wire:submit.prevent="storeSchedule" class="space-y-4 text-xs">
                    <div>
                        <label class="block font-semibold mb-1 text-gray-700 dark:text-gray-300">Judul Agenda</label>
                        <input type="text" wire:model="title" class="w-full rounded-lg border-gray-300 dark:border-zinc-700 dark:bg-zinc-800" placeholder="misal: Inspeksi Kamar A101">
                        @error('title') <span class="text-red-500 text-[10px]">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block font-semibold mb-1 text-gray-700 dark:text-gray-300">Properti / Gedung</label>
                        <select wire:model="property_id" class="w-full rounded-lg border-gray-300 dark:border-zinc-700 dark:bg-zinc-800">
                            <option value="">-- Pilih Properti --</option>
                            @foreach($properties as $p)
                                <option value="{{ $p->id }}">{{ $p->name }}</option>
                            @endforeach
                        </select>
                        @error('property_id') <span class="text-red-500 text-[10px]">{{ $message }}</span> @enderror
                    </div>

                    <div class="grid grid-cols-2 gap-2">
                        <div>
                            <label class="block font-semibold mb-1 text-gray-700 dark:text-gray-300">Tanggal</label>
                            <input type="date" wire:model="schedule_date" class="w-full rounded-lg border-gray-300 dark:border-zinc-700 dark:bg-zinc-800">
                        </div>
                        <div>
                            <label class="block font-semibold mb-1 text-gray-700 dark:text-gray-300">Kategori</label>
                            <select wire:model="type" class="w-full rounded-lg border-gray-300 dark:border-zinc-700 dark:bg-zinc-800">
                                <option value="survey">Survey / Visit</option>
                                <option value="maintenance">Maintenance</option>
                                <option value="lease_due">Jatuh Tempo</option>
                                <option value="other">Lainnya</option>
                            </select>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-2">
                        <div>
                            <label class="block font-semibold mb-1 text-gray-700 dark:text-gray-300">Jam Mulai</label>
                            <input type="time" wire:model="start_time" class="w-full rounded-lg border-gray-300 dark:border-zinc-700 dark:bg-zinc-800">
                        </div>
                        <div>
                            <label class="block font-semibold mb-1 text-gray-700 dark:text-gray-300">Jam Selesai</label>
                            <input type="time" wire:model="end_time" class="w-full rounded-lg border-gray-300 dark:border-zinc-700 dark:bg-zinc-800">
                        </div>
                    </div>

                    <div class="flex items-center justify-end gap-2 pt-2">
                        <button type="button" wire:click="closeModal" class="px-4 py-2 bg-gray-100 dark:bg-zinc-800 rounded-lg text-gray-600 dark:text-gray-300 font-semibold">
                            Batal
                        </button>
                        <button type="submit" wire:loading.attr="disabled" wire:target="storeSchedule" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg font-semibold disabled:opacity-50">
                            <span wire:loading.remove wire:target="storeSchedule">Simpan Jadwal</span>
                            <span wire:loading wire:target="storeSchedule">Menyimpan...</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>