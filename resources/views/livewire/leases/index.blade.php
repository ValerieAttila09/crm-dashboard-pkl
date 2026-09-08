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
            <tbody class="divide-y divide-gray-100 dark:divide-zinc-800 text-xs">
                @forelse($leases as $lease)
                    <tr class="hover:bg-gray-50/50 dark:hover:bg-zinc-800/40 transition">
                        <!-- Tenant Name & Phone -->
                        <td class="p-3 font-medium text-gray-800 dark:text-gray-200">
                            <p class="font-bold text-sm">{{ $lease->tenant->name ?? 'N/A' }}</p>
                            <p class="text-gray-400 text-[11px]">{{ $lease->tenant->phone ?? 'Tidak ada No. WA' }}</p>
                        </td>

                        <!-- Room & Property -->
                        <td class="p-3">
                            <span class="font-bold text-indigo-600 dark:text-indigo-400">Kamar {{ $lease->room->room_number ?? '-' }}</span>
                            <p class="text-gray-400 text-[11px]">{{ $lease->room->property->name ?? 'Properti N/A' }}</p>
                        </td>

                        <!-- Periode Sewa -->
                        <td class="p-3 text-gray-600 dark:text-gray-300">
                            <p>{{ \Carbon\Carbon::parse($lease->start_date)->format('d M Y') }} - {{ \Carbon\Carbon::parse($lease->end_date)->format('d M Y') }}</p>
                        </td>

                        <!-- Biaya Bulanan -->
                        <td class="p-3 font-semibold text-gray-800 dark:text-gray-100">
                            Rp {{ number_format($lease->monthly_rent, 0, ',', '.') }}/bln
                        </td>

                        <!-- Status Pembayaran + Quick Toggle -->
                        <td class="p-3">
                            <div class="inline-flex items-center gap-1">
                                @if($lease->payment_status === 'paid')
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-extrabold bg-emerald-100 text-emerald-800 dark:bg-emerald-950 dark:text-emerald-300">
                                        PAID
                                    </span>
                                @elseif($lease->payment_status === 'unpaid')
                                    <button wire:click="togglePaymentStatus('{{ $lease->id }}', 'paid')" 
                                            class="px-2 py-0.5 rounded-full text-[10px] font-extrabold bg-amber-100 text-amber-800 hover:bg-emerald-100 hover:text-emerald-800 transition title='Klik untuk tandai Lunas'">
                                        UNPAID ➔
                                    </button>
                                @else
                                    <button wire:click="togglePaymentStatus('{{ $lease->id }}', 'paid')" 
                                            class="px-2 py-0.5 rounded-full text-[10px] font-extrabold bg-red-100 text-red-800 hover:bg-emerald-100 hover:text-emerald-800 transition title='Klik untuk tandai Lunas'">
                                        OVERDUE ➔
                                    </button>
                                @endif
                            </div>
                        </td>

                        <!-- Status Kontrak & Tombol Aksi (WhatsApp & Terminate) -->
                        <td class="p-3">
                            <div class="flex items-center gap-2">
                                <!-- Tombol Kirim WA -->
                                @if($lease->tenant && $lease->tenant->phone)
                                    <a href="{{ $this->getWaReminderUrl($lease->id) }}" target="_blank" 
                                    class="p-1.5 bg-emerald-50 text-emerald-600 hover:bg-emerald-100 rounded-lg transition" title="Kirim Tagihan via WA">
                                        💬 WA
                                    </a>
                                @endif

                                <!-- Tombol Akhiri Kontrak -->
                                @if($lease->status === 'active')
                                    <button wire:click="terminateLease('{{ $lease->id }}')" 
                                            wire:confirm="Apakah Anda yakin ingin mengakhiri kontrak sewa ini? Status kamar akan otomatis kembali Available."
                                            class="p-1.5 bg-red-50 text-red-600 hover:bg-red-100 rounded-lg transition font-medium" title="Akhiri Masa Sewa">
                                        Akhiri
                                    </button>
                                @else
                                    <span class="text-gray-400 text-[10px] italic">Selesai</span>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="p-8 text-center text-gray-400">Belum ada data kontrak sewa.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Modal Form -->
   @if($isModalOpen)
        <div class="fixed inset-0 z-50 overflow-y-auto bg-gray-900/60 backdrop-blur-sm flex justify-center items-center p-4">
            <div class="bg-white dark:bg-zinc-900 w-full max-w-lg rounded-2xl p-6 shadow-2xl border border-gray-200 dark:border-zinc-800">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-base font-bold text-gray-900 dark:text-white">
                        {{ $leaseId ? 'Edit Kontrak Sewa' : 'Buat Kontrak Sewa Baru' }}
                    </h3>
                    <button wire:click="closeModal" class="text-gray-400 hover:text-gray-600 text-sm">✕</button>
                </div>

                <form wire:submit.prevent="store" class="space-y-4 text-xs">
                    
                    <!-- Pilih Penyewa / Tenant -->
                    <div>
                        <div class="flex items-center justify-between mb-1">
                            <label class="font-semibold text-gray-700 dark:text-gray-300">Pilih Penyewa (Tenant)</label>
                            <button type="button" wire:click="toggleCreateTenant" class="text-indigo-600 dark:text-indigo-400 font-semibold text-[11px] hover:underline">
                                {{ $isCreatingTenant ? 'Cancel' : '+ Penyewa Baru' }}
                            </button>
                        </div>

                        @if($isCreatingTenant)
                            <div class="p-3 bg-gray-50 dark:bg-zinc-800 rounded-xl space-y-2 border border-gray-200 dark:border-zinc-700 mb-2">
                                <input type="text" wire:model="new_tenant_name" class="w-full rounded-lg border-gray-300 dark:border-zinc-700 dark:bg-zinc-900 text-xs" placeholder="Nama Lengkap Tenant">
                                <input type="email" wire:model="new_tenant_email" class="w-full rounded-lg border-gray-300 dark:border-zinc-700 dark:bg-zinc-900 text-xs" placeholder="Alamat Email">
                                <input type="text" wire:model="new_tenant_phone" class="w-full rounded-lg border-gray-300 dark:border-zinc-700 dark:bg-zinc-900 text-xs" placeholder="Nomor WhatsApp">
                                <button type="button" wire:click="storeTenant" class="w-full py-1.5 bg-indigo-600 text-white rounded-lg font-semibold text-xs">Simpan Tenant</button>
                            </div>
                        @else
                            <select wire:model="customer_id" class="w-full rounded-lg border-gray-300 dark:border-zinc-700 dark:bg-zinc-800">
                                <option value="">-- Pilih Penyewa --</option>
                                @foreach($tenants as $tenant)
                                    <option value="{{ $tenant->id }}">{{ $tenant->name }} ({{ $tenant->email }})</option>
                                @endforeach
                            </select>
                        @endif
                        @error('customer_id') <span class="text-red-500 text-[10px]">{{ $message }}</span> @enderror
                    </div>

                    <!-- Pilih Kamar -->
                    <div>
                        <label class="block font-semibold mb-1 text-gray-700 dark:text-gray-300">Pilih Kamar</label>
                        <select wire:model.live="room_id" class="w-full rounded-lg border-gray-300 dark:border-zinc-700 dark:bg-zinc-800">
                            <option value="">-- Pilih Kamar --</option>
                            @foreach($availableRooms as $room)
                                <option value="{{ $room->id }}">
                                    Kamar {{ $room->room_number }} - {{ $room->property->name ?? 'N/A' }} (Rp {{ number_format($room->price_per_month, 0, ',', '.') }}/bln)
                                </option>
                            @endforeach
                        </select>
                        @error('room_id') <span class="text-red-500 text-[10px]">{{ $message }}</span> @enderror
                    </div>

                    <!-- Tanggal Mulai & Berakhir -->
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block font-semibold mb-1 text-gray-700 dark:text-gray-300">Tanggal Mulai</label>
                            <input type="date" wire:model="start_date" class="w-full rounded-lg border-gray-300 dark:border-zinc-700 dark:bg-zinc-800">
                            @error('start_date') <span class="text-red-500 text-[10px]">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="block font-semibold mb-1 text-gray-700 dark:text-gray-300">Tanggal Berakhir</label>
                            <input type="date" wire:model="end_date" class="w-full rounded-lg border-gray-300 dark:border-zinc-700 dark:bg-zinc-800">
                            @error('end_date') <span class="text-red-500 text-[10px]">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <!-- Biaya & Status Pembayaran -->
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block font-semibold mb-1 text-gray-700 dark:text-gray-300">Sewa / Bulan (Rp)</label>
                            <input type="number" wire:model="monthly_rent" class="w-full rounded-lg border-gray-300 dark:border-zinc-700 dark:bg-zinc-800">
                            @error('monthly_rent') <span class="text-red-500 text-[10px]">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="block font-semibold mb-1 text-gray-700 dark:text-gray-300">Status Pembayaran</label>
                            <select wire:model="payment_status" class="w-full rounded-lg border-gray-300 dark:border-zinc-700 dark:bg-zinc-800">
                                <option value="unpaid">Unpaid</option>
                                <option value="paid">Paid</option>
                                <option value="overdue">Overdue</option>
                            </select>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="flex items-center justify-end gap-2 pt-3 border-t border-gray-100 dark:border-zinc-800">
                        <button type="button" wire:click="closeModal" class="px-4 py-2 bg-gray-100 dark:bg-zinc-800 rounded-lg text-gray-600 dark:text-gray-300 font-semibold">
                            Batal
                        </button>
                        <button type="submit" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg font-semibold">
                            Simpan Kontrak
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>