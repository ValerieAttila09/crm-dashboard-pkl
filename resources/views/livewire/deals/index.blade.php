<?php

use Livewire\Component;

new class extends Component
{
    //
};
?>

<div class="p-6 space-y-6">
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-2xl font-bold text-gray-800 dark:text-white">Sales Pipeline (Deals)</h1>
            <p class="text-sm text-gray-500 dark:text-gray-400">Kelola prospek dan alur penjualan bisnis Anda</p>
        </div>
        <button wire:click="create()" class="px-4 py-2 bg-indigo-600 text-white font-medium rounded-lg hover:bg-indigo-700 transition">
            + Tambah Deal
        </button>
    </div>

    @if (session()->has('message'))
        <div class="p-4 bg-emerald-100 border border-emerald-400 text-emerald-700 rounded-lg">
            {{ session('message') }}
        </div>
    @endif

    <!-- Kanban Board Grid -->
    <div class="grid grid-cols-1 md:grid-cols-5 gap-4 overflow-x-auto pb-4">
        @foreach($stages as $stg)
            <div class="bg-gray-50 dark:bg-zinc-900/50 p-4 rounded-xl border border-gray-200 dark:border-zinc-800 min-w-[260px] flex flex-col justify-between">
                <div>
                    <!-- Stage Header -->
                    <div class="flex justify-between items-center mb-4 pb-2 border-b border-gray-200 dark:border-zinc-800">
                        <h3 class="font-semibold text-sm uppercase tracking-wider text-gray-700 dark:text-gray-300">
                            {{ ucfirst($stg) }}
                        </h3>
                        <span class="text-xs px-2 py-0.5 bg-gray-200 dark:bg-zinc-800 text-gray-600 dark:text-gray-400 rounded-full font-bold">
                            {{ isset($deals[$stg]) ? $deals[$stg]->count() : 0 }}
                        </span>
                    </div>

                    <!-- Deals Cards List -->
                    <div class="space-y-3">
                        @forelse($deals[$stg] ?? [] as $deal)
                            <div class="bg-white dark:bg-zinc-800 p-4 rounded-lg shadow-sm border border-gray-100 dark:border-zinc-700 hover:shadow-md transition space-y-2">
                                <div class="flex justify-between items-start">
                                    <h4 class="font-medium text-gray-900 dark:text-white text-sm">{{ $deal->title }}</h4>
                                    <div class="flex items-center space-x-1 text-xs">
                                        <button wire:click="edit('{{ $deal->id }}')" class="text-gray-400 hover:text-indigo-600">Edit</button>
                                        <button wire:click="delete('{{ $deal->id }}')" wire:confirm="Hapus deal ini?" class="text-gray-400 hover:text-red-600">Hapus</button>
                                    </div>
                                </div>

                                <p class="text-xs text-indigo-600 dark:text-indigo-400 font-semibold">
                                    {{ $deal->customer->name ?? 'No Customer' }}
                                </p>

                                <div class="flex justify-between items-center pt-2 text-xs border-t border-gray-50 dark:border-zinc-700/50 text-gray-500">
                                    <span class="font-bold text-emerald-600 dark:text-emerald-400">
                                        Rp {{ number_format($deal->amount, 0, ',', '.') }}
                                    </span>
                                    @if($deal->expected_close_date)
                                        <span>{{ $deal->expected_close_date->format('d M Y') }}</span>
                                    @endif
                                </div>

                                <!-- Quick Stage Selector Dropdown -->
                                <div class="pt-2">
                                    <select wire:change="updateStage('{{ $deal->id }}', $event.target.value)" class="w-full text-xs border border-gray-200 dark:border-zinc-700 rounded bg-gray-50 dark:bg-zinc-900 text-gray-600 dark:text-gray-300 p-1">
                                        @foreach($stages as $s)
                                            <option value="{{ $s }}" {{ $deal->stage === $s ? 'selected' : '' }}>
                                                Pindah ke: {{ ucfirst($s) }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-6 border-2 border-dashed border-gray-200 dark:border-zinc-800 rounded-lg text-xs text-gray-400">
                                Belum ada deal
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <!-- Modal Form Deal -->
    @if($isModalOpen)
    <div class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50">
        <div class="bg-white dark:bg-zinc-800 rounded-xl shadow-lg w-full max-w-md p-6 space-y-4">
            <h2 class="text-xl font-bold text-gray-800 dark:text-white">{{ $dealId ? 'Edit Deal' : 'Tambah Deal' }}</h2>
            
            <form wire:submit.prevent="store" class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Judul Deal</label>
                    <input type="text" wire:model="title" class="w-full mt-1 border dark:border-zinc-700 rounded-lg p-2 text-sm dark:bg-zinc-900 dark:text-white">
                    @error('title') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Pelanggan</label>
                    <select wire:model="customer_id" class="w-full mt-1 border dark:border-zinc-700 rounded-lg p-2 text-sm dark:bg-zinc-900 dark:text-white">
                        <option value="">-- Pilih Pelanggan --</option>
                        @foreach($customers as $c)
                            <option value="{{ $c->id }}">{{ $c->name }} ({{ $c->company ?? 'Perorangan' }})</option>
                        @endforeach
                    </select>
                    @error('customer_id') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Nilai Deal (Rp)</label>
                    <input type="number" wire:model="amount" class="w-full mt-1 border dark:border-zinc-700 rounded-lg p-2 text-sm dark:bg-zinc-900 dark:text-white">
                    @error('amount') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Tahap (Stage)</label>
                    <select wire:model="stage" class="w-full mt-1 border dark:border-zinc-700 rounded-lg p-2 text-sm dark:bg-zinc-900 dark:text-white">
                        <option value="lead">Lead</option>
                        <option value="proposal">Proposal</option>
                        <option value="negotiation">Negotiation</option>
                        <option value="won">Won</option>
                        <option value="lost">Lost</option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Perkiraan Tanggal Selesai</label>
                    <input type="date" wire:model="expected_close_date" class="w-full mt-1 border dark:border-zinc-700 rounded-lg p-2 text-sm dark:bg-zinc-900 dark:text-white">
                </div>

                <div class="flex justify-end space-x-2 pt-4">
                    <button type="button" wire:click="closeModal()" class="px-4 py-2 border rounded-lg text-sm text-gray-600 dark:text-gray-400">Batal</button>
                    <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-lg text-sm">Simpan</button>
                </div>
            </form>
        </div>
    </div>
    @endif
</div>