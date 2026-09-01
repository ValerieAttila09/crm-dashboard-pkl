<?php

use Livewire\Component;

new class extends Component
{
    //
};
?>

<div class="p-6">
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Laporan Keluhan & Perbaikan</h1>
            <p class="text-xs text-gray-500 dark:text-gray-400">Pantau dan kelola perbaikan fasilitas unit kamar.</p>
        </div>
        <button wire:click="create" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-semibold rounded-lg shadow">
            + Laporkan Kerusakan
        </button>
    </div>

    <!-- Filter Bar -->
    <div class="flex flex-col md:flex-row gap-3 mb-6">
        <input type="text" wire:model.live.debounce.300ms="search" placeholder="Cari judul / nomor kamar..." class="px-3 py-2 border rounded-lg text-xs dark:bg-zinc-800 dark:border-zinc-700 dark:text-white flex-1">
        <select wire:model.live="statusFilter" class="px-3 py-2 border rounded-lg text-xs dark:bg-zinc-800 dark:border-zinc-700 dark:text-white">
            <option value="">Semua Status</option>
            <option value="pending">Pending</option>
            <option value="in_progress">Dalam Pengerjaan</option>
            <option value="completed">Selesai</option>
            <option value="cancelled">Dibatalkan</option>
        </select>
        <select wire:model.live="priorityFilter" class="px-3 py-2 border rounded-lg text-xs dark:bg-zinc-800 dark:border-zinc-700 dark:text-white">
            <option value="">Semua Prioritas</option>
            <option value="low">Rendah (Low)</option>
            <option value="medium">Sedang (Medium)</option>
            <option value="high">Tinggi (High)</option>
            <option value="urgent">Mendesak (Urgent)</option>
        </select>
    </div>

    <!-- Table -->
    <div class="bg-white dark:bg-zinc-800 rounded-xl border border-gray-200 dark:border-zinc-700 overflow-hidden shadow-sm">
        <table class="w-full text-left text-xs text-gray-600 dark:text-gray-300">
            <thead class="bg-gray-50 dark:bg-zinc-900 border-b border-gray-200 dark:border-zinc-700 uppercase font-bold text-[10px]">
                <tr>
                    <th class="p-3">Judul & Detail</th>
                    <th class="p-3">Kamar</th>
                    <th class="p-3">Pelapor</th>
                    <th class="p-3">Prioritas</th>
                    <th class="p-3">Biaya</th>
                    <th class="p-3">Status</th>
                    <th class="p-3 text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 dark:divide-zinc-700/50">
                @forelse($requests as $req)
                    <tr class="hover:bg-gray-50 dark:hover:bg-zinc-700/30">
                        <td class="p-3">
                            <span class="font-bold text-gray-900 dark:text-white block">{{ $req->title }}</span>
                            <span class="text-[10px] text-gray-400 line-clamp-1">{{ $req->description }}</span>
                        </td>
                        <td class="p-3 font-semibold">
                            Kamar {{ $req->room->room_number ?? '-' }}
                        </td>
                        <td class="p-3">
                            {{ $req->tenant->name ?? 'Pengelola' }}
                        </td>
                        <td class="p-3">
                            <span class="px-2 py-0.5 text-[10px] font-bold rounded-full 
                                {{ $req->priority === 'urgent' ? 'bg-red-100 text-red-800' : ($req->priority === 'high' ? 'bg-amber-100 text-amber-800' : 'bg-gray-100 text-gray-800') }}">
                                {{ strtoupper($req->priority) }}
                            </span>
                        </td>
                        <td class="p-3 font-semibold text-gray-900 dark:text-white">
                            Rp {{ number_format($req->cost, 0, ',', '.') }}
                        </td>
                        <td class="p-3">
                            <span class="px-2 py-0.5 text-[10px] font-bold rounded-full uppercase
                                {{ $req->status === 'completed' ? 'bg-emerald-100 text-emerald-800' : ($req->status === 'in_progress' ? 'bg-indigo-100 text-indigo-800' : 'bg-gray-100 text-gray-800') }}">
                                {{ $req->status }}
                            </span>
                        </td>
                        <td class="p-3 text-right">
                            <button wire:click="edit('{{ $req->id }}')" class="px-2.5 py-1 bg-gray-200 dark:bg-zinc-700 hover:bg-gray-300 text-xs font-semibold rounded text-gray-800 dark:text-gray-200">
                                Edit
                            </button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="p-4 text-center text-gray-400">Belum ada laporan kerusakan.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Modal Form -->
    @if($isModalOpen)
        <div class="fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4">
            <div class="bg-white dark:bg-zinc-800 rounded-xl p-6 w-full max-w-lg shadow-xl border border-zinc-700">
                <h2 class="text-lg font-bold text-gray-900 dark:text-white mb-4">{{ $requestId ? 'Edit Laporan Perbaikan' : 'Laporkan Kerusakan Baru' }}</h2>

                <div class="space-y-3 text-xs">
                    <div>
                        <label class="block font-semibold mb-1 dark:text-gray-300">Judul Kerusakan</label>
                        <input type="text" wire:model="title" placeholder="misal: AC Tidak Dingin / Kran Bocor" class="w-full p-2 border rounded dark:bg-zinc-900 dark:border-zinc-700 dark:text-white">
                        @error('title') <span class="text-red-500 text-[10px]">{{ $message }}</span> @enderror
                    </div>

                    <div class="grid grid-cols-2 gap-2">
                        <div>
                            <label class="block font-semibold mb-1 dark:text-gray-300">Kamar</label>
                            <select wire:model="room_id" class="w-full p-2 border rounded dark:bg-zinc-900 dark:border-zinc-700 dark:text-white">
                                <option value="">-- Pilih Kamar --</option>
                                @foreach($rooms as $r)
                                    <option value="{{ $r->id }}">Kamar {{ $r->room_number }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block font-semibold mb-1 dark:text-gray-300">Pelapor (Penyewa)</label>
                            <select wire:model="customer_id" class="w-full p-2 border rounded dark:bg-zinc-900 dark:border-zinc-700 dark:text-white">
                                <option value="">-- Pengelola / None --</option>
                                @foreach($tenants as $t)
                                    <option value="{{ $t->id }}">{{ $t->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div>
                        <label class="block font-semibold mb-1 dark:text-gray-300">Deskripsi Masalah</label>
                        <textarea wire:model="description" rows="3" placeholder="Jelaskan detail kerusakan..." class="w-full p-2 border rounded dark:bg-zinc-900 dark:border-zinc-700 dark:text-white"></textarea>
                    </div>

                    <div class="grid grid-cols-3 gap-2">
                        <div>
                            <label class="block font-semibold mb-1 dark:text-gray-300">Prioritas</label>
                            <select wire:model="priority" class="w-full p-2 border rounded dark:bg-zinc-900 dark:border-zinc-700 dark:text-white">
                                <option value="low">Low</option>
                                <option value="medium">Medium</option>
                                <option value="high">High</option>
                                <option value="urgent">Urgent</option>
                            </select>
                        </div>
                        <div>
                            <label class="block font-semibold mb-1 dark:text-gray-300">Status</label>
                            <select wire:model="status" class="w-full p-2 border rounded dark:bg-zinc-900 dark:border-zinc-700 dark:text-white">
                                <option value="pending">Pending</option>
                                <option value="in_progress">In Progress</option>
                                <option value="completed">Completed</option>
                                <option value="cancelled">Cancelled</option>
                            </select>
                        </div>
                        <div>
                            <label class="block font-semibold mb-1 dark:text-gray-300">Estimasi Biaya (Rp)</label>
                            <input type="number" wire:model="cost" class="w-full p-2 border rounded dark:bg-zinc-900 dark:border-zinc-700 dark:text-white">
                        </div>
                    </div>
                </div>

                <div class="flex justify-end gap-2 mt-6">
                    <button wire:click="closeModal" class="px-4 py-2 bg-gray-200 text-xs font-semibold rounded">Batal</button>
                    <button wire:click="store" class="px-4 py-2 bg-indigo-600 text-white text-xs font-semibold rounded">Simpan Laporan</button>
                </div>
            </div>
        </div>
    @endif
</div>