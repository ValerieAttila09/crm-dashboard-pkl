<?php

use Livewire\Component;

new class extends Component
{
    //
};
?>

<div class="p-6 space-y-6">
    <!-- Header Page -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-800 dark:text-white">Riwayat & Log Aktivitas Tim</h1>
            <p class="text-sm text-gray-500 dark:text-gray-400">Pusat pemantauan seluruh riwayat interaksi pelanggan (Call, Email, Meeting, Note)</p>
        </div>
        <div class="flex flex-wrap items-center gap-3">
            <!-- Filter Jenis Aktivitas -->
            <select wire:model.live="typeFilter" class="px-3 py-2 text-xs border dark:border-zinc-700 rounded-lg dark:bg-zinc-900 dark:text-white focus:outline-none">
                <option value="">Semua Jenis Aktivitas</option>
                <option value="call">Panggilan (Call)</option>
                <option value="email">Email</option>
                <option value="meeting">Rapat (Meeting)</option>
                <option value="note">Catatan (Note)</option>
            </select>

            <!-- Search Bar -->
            <input type="text" wire:model.live.debounce.300ms="search" placeholder="Cari catatan atau pelanggan..." class="px-3 py-2 text-xs border dark:border-zinc-700 rounded-lg dark:bg-zinc-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-indigo-500">
        </div>
    </div>

    @if (session()->has('message'))
        <div class="p-3 bg-emerald-100 border border-emerald-400 text-emerald-700 text-xs rounded-lg">
            {{ session('message') }}
        </div>
    @endif

    <!-- Timeline List -->
    <div class="bg-white dark:bg-zinc-900 rounded-xl border border-gray-100 dark:border-zinc-800 shadow-sm overflow-hidden">
        <div class="divide-y divide-gray-100 dark:divide-zinc-800">
            @forelse($interactions as $item)
                <div class="p-4 hover:bg-gray-50 dark:hover:bg-zinc-800/50 transition flex items-start space-x-4">
                    <!-- Badge Icon/Type -->
                    <div class="pt-0.5">
                        <span class="px-2.5 py-1 rounded-md text-[11px] font-bold uppercase tracking-wider
                            {{ $item->type === 'call' ? 'bg-blue-100 text-blue-700 dark:bg-blue-900/40 dark:text-blue-300' : '' }}
                            {{ $item->type === 'email' ? 'bg-purple-100 text-purple-700 dark:bg-purple-900/40 dark:text-purple-300' : '' }}
                            {{ $item->type === 'meeting' ? 'bg-amber-100 text-amber-700 dark:bg-amber-900/40 dark:text-amber-300' : '' }}
                            {{ $item->type === 'note' ? 'bg-gray-100 text-gray-700 dark:bg-zinc-800 dark:text-gray-300' : '' }}">
                            {{ $item->type }}
                        </span>
                    </div>

                    <!-- Content -->
                    <div class="flex-1 space-y-1">
                        <div class="flex items-center justify-between">
                            <div>
                                @if($item->customer)
                                    <a href="{{ route('customers.show', ['current_team' => auth()->user()->currentTeam->slug, 'id' => $item->customer->id]) }}" wire:navigate class="font-semibold text-xs text-indigo-600 dark:text-indigo-400 hover:underline">
                                        {{ $item->customer->name }}
                                    </a>
                                    @if($item->customer->company)
                                        <span class="text-[11px] text-gray-400"> ({{ $item->customer->company }})</span>
                                    @endif
                                @else
                                    <span class="font-semibold text-xs text-gray-500">Pelanggan Dihapus</span>
                                @endif
                            </div>
                            <span class="text-[11px] text-gray-400">{{ $item->created_at->format('d M Y, H:i') }} ({{ $item->created_at->diffForHumans() }})</span>
                        </div>
                        <p class="text-xs text-gray-700 dark:text-gray-300 leading-relaxed">{{ $item->notes }}</p>
                    </div>

                    <!-- Action Hapus -->
                    <div>
                        <button wire:click="delete('{{ $item->id }}')" wire:confirm="Yakin ingin menghapus log ini?" class="text-gray-400 hover:text-red-600 text-xs px-2 py-1">
                            &times;
                        </button>
                    </div>
                </div>
            @empty
                <div class="text-center py-12 text-xs text-gray-400">
                    Belum ada log aktivitas yang cocok dengan pencarian atau filter Anda.
                </div>
            @endforelse
        </div>

        @if($interactions->hasPages())
            <div class="p-4 border-t border-gray-100 dark:border-zinc-800">
                {{ $interactions->links() }}
            </div>
        @endif
    </div>
</div>