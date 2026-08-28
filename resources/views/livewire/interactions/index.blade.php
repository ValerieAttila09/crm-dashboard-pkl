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
            <h1 class="text-2xl font-bold text-gray-800 dark:text-white">Timeline & Log Aktivitas Tim</h1>
            <p class="text-sm text-gray-500 dark:text-gray-400">Pantau dan catat seluruh riwayat komunikasi dengan pelanggan</p>
        </div>
        <div>
            <button wire:click="openModal" class="px-4 py-2 bg-indigo-600 text-white font-medium text-xs rounded-lg hover:bg-indigo-700 transition flex items-center gap-1.5">
                + Catat Aktivitas Baru
            </button>
        </div>
    </div>

    @if (session()->has('message'))
        <div class="p-3 bg-emerald-100 border border-emerald-400 text-emerald-700 text-xs rounded-lg">
            {{ session('message') }}
        </div>
    @endif

    @if (session()->has('error'))
        <div class="p-3 bg-red-100 border border-red-400 text-red-700 text-xs rounded-lg">
            {{ session('error') }}
        </div>
    @endif

    <!-- Toolbar Filter & Search -->
    <div class="bg-white dark:bg-zinc-900 p-4 rounded-xl border border-gray-100 dark:border-zinc-800 shadow-sm flex flex-wrap items-center justify-between gap-3">
        <div class="flex flex-wrap items-center gap-3">
            <!-- Filter Jenis -->
            <select wire:model.live="typeFilter" class="px-3 py-2 text-xs border dark:border-zinc-700 rounded-lg dark:bg-zinc-900 dark:text-white focus:outline-none">
                <option value="">Semua Jenis Aktivitas</option>
                <option value="call">Panggilan (Call)</option>
                <option value="email">Email</option>
                <option value="meeting">Rapat (Meeting)</option>
                <option value="note">Catatan (Note)</option>
            </select>

            <!-- Filter Tanggal -->
            <div class="flex items-center space-x-2 text-xs text-gray-500">
                <input type="date" wire:model.live="dateFrom" class="px-2.5 py-1.5 border dark:border-zinc-700 rounded-lg dark:bg-zinc-900 dark:text-white focus:outline-none">
                <span>s/d</span>
                <input type="date" wire:model.live="dateTo" class="px-2.5 py-1.5 border dark:border-zinc-700 rounded-lg dark:bg-zinc-900 dark:text-white focus:outline-none">
            </div>
        </div>

        <!-- Search Bar -->
        <input type="text" wire:model.live.debounce.300ms="search" placeholder="Cari catatan atau nama..." class="px-3 py-2 text-xs border dark:border-zinc-700 rounded-lg dark:bg-zinc-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-indigo-500 min-w-[220px]">
    </div>

    <!-- Timeline List Section -->
    <div class="bg-white dark:bg-zinc-900 rounded-xl border border-gray-100 dark:border-zinc-800 shadow-sm overflow-hidden">
        <div class="divide-y divide-gray-100 dark:divide-zinc-800">
            @forelse($interactions as $item)
                <div class="p-4 hover:bg-gray-50 dark:hover:bg-zinc-800/50 transition flex items-start space-x-4">
                    <!-- Badge Icon/Type -->
                    <div class="pt-0.5">
                        <!-- Ganti bagian penampil Badge Type -->
                        <span class="px-2.5 py-1 rounded-md text-[10px] font-bold uppercase tracking-wider
                            {{ str_starts_with($item->notes, 'SYSTEM:') ? 'bg-zinc-800 text-zinc-100 dark:bg-zinc-100 dark:text-zinc-900 border border-zinc-700' : '' }}
                            {{ $item->type === 'call' && !str_starts_with($item->notes, 'SYSTEM:') ? 'bg-blue-100 text-blue-700 dark:bg-blue-900/40 dark:text-blue-300' : '' }}
                            {{ $item->type === 'email' && !str_starts_with($item->notes, 'SYSTEM:') ? 'bg-purple-100 text-purple-700 dark:bg-purple-900/40 dark:text-purple-300' : '' }}
                            {{ $item->type === 'meeting' && !str_starts_with($item->notes, 'SYSTEM:') ? 'bg-amber-100 text-amber-700 dark:bg-amber-900/40 dark:text-amber-300' : '' }}
                            {{ $item->type === 'note' && !str_starts_with($item->notes, 'SYSTEM:') ? 'bg-gray-100 text-gray-700 dark:bg-zinc-800 dark:text-gray-300' : '' }}">
                            {{ str_starts_with($item->notes, 'SYSTEM:') ? 'SYSTEM LOG' : $item->type }}
                        </span>
                    </div>

                    <!-- Content Details -->
                    <div class="flex-1 space-y-1">
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-1">
                            <div>
                                @if($item->customer)
                                    <a href="{{ route('customers.show', ['current_team' => auth()->user()->currentTeam->slug, 'id' => $item->customer->id]) }}" wire:navigate class="font-bold text-xs text-indigo-600 dark:text-indigo-400 hover:underline">
                                        {{ $item->customer->name }}
                                    </a>
                                    @if($item->customer->company)
                                        <span class="text-[11px] text-gray-400"> ({{ $item->customer->company }})</span>
                                    @endif
                                @else
                                    <span class="font-semibold text-xs text-gray-500">Pelanggan Dihapus</span>
                                @endif
                            </div>
                            
                            <div class="flex items-center space-x-2 text-[11px] text-gray-400">
                                @if($item->user)
                                    <span class="bg-gray-100 dark:bg-zinc-800 px-1.5 py-0.5 rounded text-gray-600 dark:text-gray-300">
                                        Oleh: {{ $item->user->name }}
                                    </span>
                                @endif
                                <span>{{ $item->created_at->format('d M Y, H:i') }}</span>
                            </div>
                        </div>

                        <p class="text-xs text-gray-700 dark:text-gray-300 leading-relaxed pt-1">{{ $item->notes }}</p>
                    </div>

                    <!-- Action Hapus (Restricted Admin) -->
                    @if(auth()->user()->isTeamAdmin())
                        <div>
                            <button wire:click="delete('{{ $item->id }}')" wire:confirm="Yakin ingin menghapus log ini?" class="text-gray-400 hover:text-red-600 text-sm px-2 py-1">
                                &times;
                            </button>
                        </div>
                    @endif
                </div>
            @empty
                <div class="text-center py-12 text-xs text-gray-400 space-y-2">
                    <p>Belum ada log aktivitas yang cocok dengan pencarian atau filter Anda.</p>
                </div>
            @endforelse
        </div>

        @if($interactions->hasPages())
            <div class="p-4 border-t border-gray-100 dark:border-zinc-800">
                {{ $interactions->links() }}
            </div>
        @endif
    </div>

    <!-- Modal Form Catat Aktivitas Cepat -->
    @if($isModalOpen)
    <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm">
        <div class="bg-white dark:bg-zinc-800 rounded-xl shadow-xl w-full max-w-md p-6 space-y-4 border border-gray-100 dark:border-zinc-700">
            <div class="flex justify-between items-center border-b dark:border-zinc-700 pb-3">
                <h3 class="text-lg font-bold text-gray-800 dark:text-white">Catat Aktivitas Komunikasi</h3>
                <button wire:click="closeModal" class="text-gray-400 hover:text-gray-600 dark:hover:text-white">&times;</button>
            </div>
            
            <form wire:submit.prevent="store" class="space-y-4">
                <div>
                    <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Pilih Pelanggan</label>
                    <select wire:model="customer_id" class="w-full border dark:border-zinc-700 rounded-lg p-2.5 text-xs dark:bg-zinc-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        <option value="">-- Pilih Pelanggan --</option>
                        @foreach($customers as $c)
                            <option value="{{ $c->id }}">{{ $c->name }} {{ $c->company ? "({$c->company})" : '' }}</option>
                        @endforeach
                    </select>
                    @error('customer_id') <span class="text-red-500 text-[11px] mt-1 block">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Tipe Interaksi</label>
                    <select wire:model="type" class="w-full border dark:border-zinc-700 rounded-lg p-2.5 text-xs dark:bg-zinc-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        <option value="call">Panggilan Telepon (Call)</option>
                        <option value="email">Email Sent/Received</option>
                        <option value="meeting">Rapat / Demo (Meeting)</option>
                        <option value="note">Catatan Internal (Note)</option>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Isi Catatan Interaksi</label>
                    <textarea wire:model="notes" rows="4" placeholder="Tuliskan hasil diskusi atau ringkasan pembicaraan..." class="w-full border dark:border-zinc-700 rounded-lg p-2.5 text-xs dark:bg-zinc-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-indigo-500"></textarea>
                    @error('notes') <span class="text-red-500 text-[11px] mt-1 block">{{ $message }}</span> @enderror
                </div>

                <div class="flex justify-end space-x-2 pt-4 border-t dark:border-zinc-700">
                    <button type="button" wire:click="closeModal" class="px-4 py-2 border rounded-lg text-xs font-medium text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-zinc-700">Batal</button>
                    <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-lg text-xs font-medium hover:bg-indigo-700 transition">Simpan Log</button>
                </div>
            </form>
        </div>
    </div>
    @endif
</div>