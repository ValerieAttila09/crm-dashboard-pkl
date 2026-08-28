<?php

use Livewire\Component;

new class extends Component
{
    //
};
?>

<div class="bg-white dark:bg-zinc-900 p-6 rounded-xl border border-gray-100 dark:border-zinc-800 shadow-sm space-y-6">
    <h3 class="font-semibold text-gray-700 dark:text-gray-300 border-b pb-2 dark:border-zinc-800">
        Riwayat Aktivitas & Catatan
    </h3>

    @if (session()->has('interaction_message'))
        <div class="p-3 bg-emerald-100 border border-emerald-400 text-emerald-700 text-xs rounded-lg">
            {{ session('interaction_message') }}
        </div>
    @endif

    <!-- Form Tambah Aktivitas -->
    <form wire:submit.prevent="store" class="space-y-3 bg-gray-50 dark:bg-zinc-800/50 p-4 rounded-lg border border-gray-100 dark:border-zinc-800">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-3">
            <div>
                <label class="block text-xs font-medium text-gray-600 dark:text-gray-400">Jenis Aktivitas</label>
                <select wire:model="type" class="w-full mt-1 border dark:border-zinc-700 rounded-lg p-2 text-xs dark:bg-zinc-900 dark:text-white">
                    <option value="note">Catatan (Note)</option>
                    <option value="call">Panggilan (Call)</option>
                    <option value="email">Email</option>
                    <option value="meeting">Rapat (Meeting)</option>
                </select>
            </div>
            <div class="md:col-span-3">
                <label class="block text-xs font-medium text-gray-600 dark:text-gray-400">Rincian Aktivitas</label>
                <input type="text" wire:model="notes" placeholder="Tuliskan catatan interaksi..." class="w-full mt-1 border dark:border-zinc-700 rounded-lg p-2 text-xs dark:bg-zinc-900 dark:text-white">
                @error('notes') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
            </div>
        </div>
        <div class="flex justify-end">
            <button type="submit" class="px-3 py-1.5 bg-indigo-600 text-white rounded-lg text-xs font-medium hover:bg-indigo-700 transition">
                + Simpan Aktivitas
            </button>
        </div>
    </form>

    <!-- Timeline Aktivitas -->
    <div class="space-y-4 pt-2">
        @forelse($interactions as $item)
            <div class="flex items-start space-x-3 text-xs border-b border-gray-50 dark:border-zinc-800 pb-3">
                <span class="px-2 py-1 rounded font-bold uppercase text-[10px] 
                    {{ $item->type === 'call' ? 'bg-blue-100 text-blue-700' : '' }}
                    {{ $item->type === 'email' ? 'bg-purple-100 text-purple-700' : '' }}
                    {{ $item->type === 'meeting' ? 'bg-amber-100 text-amber-700' : '' }}
                    {{ $item->type === 'note' ? 'bg-gray-100 text-gray-700' : '' }}">
                    {{ $item->type }}
                </span>
                <div class="flex-1">
                    <p class="text-gray-800 dark:text-gray-200 font-medium">{{ $item->notes }}</p>
                    <span class="text-[10px] text-gray-400">{{ $item->created_at->diffForHumans() }}</span>
                </div>
                <button wire:click="delete('{{ $item->id }}')" wire:confirm="Hapus catatan ini?" class="text-gray-400 hover:text-red-600">
                    &times;
                </button>
            </div>
        @empty
            <p class="text-xs text-gray-400 text-center py-4">Belum ada riwayat aktivitas tercatat.</p>
        @endforelse
    </div>
</div>