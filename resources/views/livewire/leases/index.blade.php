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
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Kontrak & Tagihan Sewa</h1>
            <p class="text-xs text-gray-500 dark:text-gray-400">Kelola masa sewa kamar dan status pembayaran penghuni.</p>
        </div>
        <button wire:click="create" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-semibold rounded-lg shadow">
            + Buat Kontrak Sewa
        </button>
    </div>

    <!-- Filter & Search -->
    <div class="flex flex-col md:flex-row gap-3 mb-6">
        <input type="text" wire:model.live.debounce.300ms="search" placeholder="Cari nama penyewa / nomor kamar..." class="px-3 py-2 border rounded-lg text-xs dark:bg-zinc-800 dark:border-zinc-700 dark:text-white flex-1">
        <select wire:model.live="statusFilter" class="px-3 py-2 border rounded-lg text-xs dark:bg-zinc-800 dark:border-zinc-700 dark:text-white">
            <option value="">Semua Status Bayar</option>
            <option value="paid">Lunas (Paid)</option>
            <option value="unpaid">Belum Bayar (Unpaid)</option>
            <option value="overdue">Tunggakan (Overdue)</option>
        </select>
    </div>

    <!-- Table -->
    <div class="bg-white dark:bg-zinc-800 rounded-xl border border-gray-200 dark:border-zinc-700 overflow-hidden shadow-sm">
        <table class="w-full text-left text-xs text-gray-600 dark:text-gray-300">
            <thead class="bg-gray-50 dark:bg-zinc-900 border-b border-gray-200 dark:border-zinc-700 uppercase font-bold text-[10px]">
                <tr>
                    <th class="p-3">Penyewa</th>
                    <th class="p-3">Kamar & Properti</th>
                    <th class="p-3">Periode Sewa</th>
                    <th class="p-3">Biaya / Bulan</th>
                    <th class="p-3">Status Bayar</th>
                    <th class="p-3">Status Kontrak</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 dark:divide-zinc-700/50">
                @forelse($leases as $lease)
                    <tr>
                        <td class="p-3 font-semibold text-gray-900 dark:text-white">
                            {{ $lease->tenant->name ?? 'N/A' }}
                        </td>
                        <td class="p-3">
                            Kamar {{ $lease->room->room_number ?? '-' }}
                            <span class="block text-[10px] text-gray-400">{{ $lease->room->property->name ?? '' }}</span>
                        </td>
                        <td class="p-3">
                            {{ $lease->start_date->format('d M Y') }} - {{ $lease->end_date->format('d M Y') }}
                        </td>
                        <td class="p-3 font-bold text-gray-900 dark:text-white">
                            Rp {{ number_format($lease->monthly_rent, 0, ',', '.') }}
                        </td>
                        <td class="p-3">
                            <span class="px-2 py-0.5 text-[10px] font-bold rounded-full 
                                {{ $lease->payment_status === 'paid' ? 'bg-emerald-100 text-emerald-800' : ($lease->payment_status === 'unpaid' ? 'bg-amber-100 text-amber-800' : 'bg-red-100 text-red-800') }}">
                                {{ strtoupper($lease->payment_status) }}
                            </span>
                        </td>
                        <td class="p-3 uppercase text-[10px] font-bold">
                            {{ $lease->status }}
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="p-4 text-center text-gray-400">Belum ada data kontrak sewa.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Modal Form -->
    @if($isModalOpen)
        <div class="fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4">
            <div class="bg-white dark:bg-zinc-800 rounded-xl p-6 w-full max-w-lg shadow-xl border border-zinc-700">
                <h2 class="text-lg font-bold text-gray-900 dark:text-white mb-4">Buat Kontrak Sewa Baru</h2>

                <div class="space-y-3 text-xs">
                    <div>
                        <label class="block font-semibold mb-1 dark:text-gray-300">Pilih Kamar</label>
                        <select wire:model.live="room_id" class="w-full p-2 border rounded dark:bg-zinc-900 dark:border-zinc-700 dark:text-white">
                            <option value="">-- Pilih Kamar --</option>
                            @foreach($availableRooms as $r)
                                <option value="{{ $r->id }}">Kamar {{ $r->room_number }} ({{ $r->property->name ?? '' }}) - Rp {{ number_format($r->price_per_month, 0, ',', '.') }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block font-semibold mb-1 dark:text-gray-300">Pilih Penyewa (Tenant)</label>
                        <select wire:model="customer_id" class="w-full p-2 border rounded dark:bg-zinc-900 dark:border-zinc-700 dark:text-white">
                            <option value="">-- Pilih Penyewa --</option>
                            @foreach($tenants as $t)
                                <option value="{{ $t->id }}">{{ $t->name }} ({{ $t->email }})</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="grid grid-cols-2 gap-2">
                        <div>
                            <label class="block font-semibold mb-1 dark:text-gray-300">Tanggal Mulai</label>
                            <input type="date" wire:model="start_date" class="w-full p-2 border rounded dark:bg-zinc-900 dark:border-zinc-700 dark:text-white">
                        </div>
                        <div>
                            <label class="block font-semibold mb-1 dark:text-gray-300">Tanggal Berakhir</label>
                            <input type="date" wire:model="end_date" class="w-full p-2 border rounded dark:bg-zinc-900 dark:border-zinc-700 dark:text-white">
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-2">
                        <div>
                            <label class="block font-semibold mb-1 dark:text-gray-300">Sewa / Bulan (Rp)</label>
                            <input type="number" wire:model="monthly_rent" class="w-full p-2 border rounded dark:bg-zinc-900 dark:border-zinc-700 dark:text-white">
                        </div>
                        <div>
                            <label class="block font-semibold mb-1 dark:text-gray-300">Status Pembayaran</label>
                            <select wire:model="payment_status" class="w-full p-2 border rounded dark:bg-zinc-900 dark:border-zinc-700 dark:text-white">
                                <option value="unpaid">Unpaid</option>
                                <option value="paid">Paid</option>
                                <option value="overdue">Overdue</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="flex justify-end gap-2 mt-6">
                    <button wire:click="closeModal" class="px-4 py-2 bg-gray-200 text-xs font-semibold rounded">Batal</button>
                    <button wire:click="store" class="px-4 py-2 bg-indigo-600 text-white text-xs font-semibold rounded">Simpan Kontrak</button>
                </div>
            </div>
        </div>
    @endif
</div>