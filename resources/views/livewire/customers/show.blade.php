<?php

use Livewire\Component;

new class extends Component
{
    //
};
?>

<div class="p-6 space-y-6">
    <!-- Header & Back Button -->
    <div class="flex justify-between items-center">
        <div class="flex items-center space-x-4">
            <a href="{{ route('customers.index', ['current_team' => auth()->user()->currentTeam->slug]) }}" wire:navigate class="px-3 py-1.5 border rounded-lg text-sm text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-zinc-800 transition">
                &larr; Kembali
            </a>
            <div>
                <h1 class="text-2xl font-bold text-gray-800 dark:text-white">{{ $customer->name }}</h1>
                <p class="text-sm text-gray-500 dark:text-gray-400">{{ $customer->company ?? 'Pelanggan Perorangan' }}</p>
            </div>
        </div>
        <div>
            <span class="px-3 py-1 text-xs rounded-full bg-blue-100 text-blue-800 uppercase font-bold">
                {{ $customer->status }}
            </span>
        </div>
    </div>

    <!-- Informasi Kontak Grid -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="bg-white dark:bg-zinc-900 p-6 rounded-xl border border-gray-100 dark:border-zinc-800 shadow-sm space-y-4">
            <h3 class="font-semibold text-gray-700 dark:text-gray-300 border-b pb-2 dark:border-zinc-800">Detail Kontak</h3>
            <div>
                <p class="text-xs text-gray-400">Email</p>
                <p class="text-sm font-medium text-gray-800 dark:text-gray-200">{{ $customer->email }}</p>
            </div>
            <div>
                <p class="text-xs text-gray-400">No. Telepon</p>
                <p class="text-sm font-medium text-gray-800 dark:text-gray-200">{{ $customer->phone ?? '-' }}</p>
            </div>
            <div>
                <p class="text-xs text-gray-400">Perusahaan</p>
                <p class="text-sm font-medium text-gray-800 dark:text-gray-200">{{ $customer->company ?? '-' }}</p>
            </div>
            <div>
                <p class="text-xs text-gray-400">Terdaftar Pada</p>
                <p class="text-sm font-medium text-gray-800 dark:text-gray-200">{{ $customer->created_at->format('d M Y, H:i') }}</p>
            </div>
        </div>

        <!-- Deals Terkait -->
        <div class="md:col-span-2 bg-white dark:bg-zinc-900 p-6 rounded-xl border border-gray-100 dark:border-zinc-800 shadow-sm space-y-4">
            <h3 class="font-semibold text-gray-700 dark:text-gray-300 border-b pb-2 dark:border-zinc-800">Riwayat Deals Penjualan</h3>
            <div class="space-y-3">
                @forelse($customer->deals as $deal)
                    <div class="p-4 border rounded-lg dark:border-zinc-800 flex justify-between items-center">
                        <div>
                            <h4 class="font-semibold text-sm text-gray-800 dark:text-gray-200">{{ $deal->title }}</h4>
                            <p class="text-xs text-gray-500">Stage: <span class="uppercase font-bold text-indigo-600">{{ $deal->stage }}</span></p>
                        </div>
                        <div class="text-right">
                            <p class="font-bold text-emerald-600 text-sm">Rp {{ number_format($deal->amount, 0, ',', '.') }}</p>
                        </div>
                    </div>
                @empty
                    <p class="text-sm text-gray-400 text-center py-6">Belum ada deal penjualan terhubung dengan pelanggan ini.</p>
                @endforelse
            </div>
        </div>

        <!-- Panggil Komponent Interactions -->
        <livewire:customers.interactions :customerId="$customer->id" />
    </div>
</div>