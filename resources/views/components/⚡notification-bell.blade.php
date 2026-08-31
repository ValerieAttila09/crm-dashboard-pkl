<?php

use Livewire\Component;

new class extends Component
{
    //
};
?>

<div class="relative" x-data="{ open: @entangle('isOpen') }">
    <!-- Tombol Lonceng Notifikasi -->
    <button @click="open = !open" class="relative p-2 text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200 focus:outline-none transition">
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
        </svg>

        @if($unreadCount > 0)
            <span class="absolute top-1 right-1 flex h-4 w-4 items-center justify-center rounded-full bg-red-500 text-[10px] font-bold text-white">
                {{ $unreadCount }}
            </span>
        @endif
    </button>

    <!-- Dropdown Daftar Notifikasi -->
    <div x-show="open" @click.away="open = false" class="absolute right-0 mt-2 w-80 bg-white dark:bg-zinc-800 rounded-xl shadow-xl border border-gray-100 dark:border-zinc-700 z-50 overflow-hidden" x-cloak>
        <div class="p-3 border-b border-gray-100 dark:border-zinc-700 flex items-center justify-between">
            <h3 class="text-xs font-bold text-gray-800 dark:text-gray-100">Notifikasi Tim</h3>
            @if($unreadCount > 0)
                <button wire:click="markAllAsRead" class="text-[11px] font-medium text-indigo-600 dark:text-indigo-400 hover:underline">
                    Tandai Semua Dibaca
                </button>
            @endif
        </div>

        <div class="max-h-64 overflow-y-auto divide-y divide-gray-100 dark:divide-zinc-700/50">
            @forelse($notifications as $notification)
                <div class="p-3 text-xs flex items-start justify-between gap-2 {{ $notification->read_at ? 'bg-white dark:bg-zinc-800 opacity-60' : 'bg-indigo-50/50 dark:bg-indigo-950/20 font-medium' }}">
                    <div>
                        <p class="text-gray-800 dark:text-gray-200">{{ $notification->data['message'] ?? 'Notifikasi baru' }}</p>
                        <span class="text-[10px] text-gray-400 mt-1 block">{{ $notification->created_at->diffForHumans() }}</span>
                    </div>
                    @if(!$notification->read_at)
                        <button wire:click="markAsRead('{{ $notification->id }}')" class="text-[10px] text-indigo-600 dark:text-indigo-400 font-semibold hover:underline">
                            Dibaca
                        </button>
                    @endif
                </div>
            @empty
                <div class="p-4 text-center text-xs text-gray-400">
                    Tidak ada notifikasi saat ini.
                </div>
            @endforelse
        </div>
    </div>
</div>