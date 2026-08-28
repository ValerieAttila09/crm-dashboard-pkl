<?php

use Livewire\Component;

new class extends Component
{
    //
};
?>

<div class="p-6 space-y-6">
    <div class="flex justify-between items-center">
        <h1 class="text-2xl font-bold text-gray-800">Manajemen Pelanggan</h1>
        <button wire:click="create()" class="px-4 py-2 bg-indigo-600 text-white font-medium rounded-lg hover:bg-indigo-700 transition">
            + Tambah Pelanggan
        </button>
    </div>

    @if (session()->has('message'))
        <div class="p-4 bg-emerald-100 border border-emerald-400 text-emerald-700 rounded-lg">
            {{ session('message') }}
        </div>
    @endif

    <!-- Search Bar -->
    <div class="flex items-center gap-2 bg-white p-4 rounded-xl shadow-sm border border-gray-100">
        <input type="text" wire:model.live="search" placeholder="Cari nama, email, atau perusahaan..." class="w-full md:w-1/3 px-4 py-2 border rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
        <div class="relative flex items-center gap-2" x-data="{ open: false }">
            <button @click="open = !open" class="px-3 py-2.5 border border-neutral-300 bg-gray-100 dark:bg-zinc-800 text-gray-700 dark:text-gray-200 text-xs font-semibold rounded-lg hover:bg-gray-200 dark:hover:bg-zinc-700 transition flex items-center gap-1.5">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                Export Data
            </button>

            <div x-show="open" @click.away="open = false" class="absolute right-0 top-full mt-2 w-44 bg-white dark:bg-zinc-800 rounded-lg shadow-xl border border-gray-100 dark:border-zinc-700 py-1 z-50 text-xs" x-cloak>
                <a href="{{ route('export.customers', ['current_team' => auth()->user()->currentTeam->slug, 'format' => 'csv']) }}" class="block px-4 py-2 text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-zinc-700">Export CSV / Excel (.csv)</a>
                <a href="{{ route('export.customers', ['current_team' => auth()->user()->currentTeam->slug, 'format' => 'json']) }}" class="block px-4 py-2 text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-zinc-700">Export JSON (.json)</a>
            </div>
        </div>
    </div>

    <!-- Table -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <table class="w-full text-left text-sm text-gray-600">
            <thead class="bg-gray-50 text-gray-700">
                <tr>
                    <th class="px-6 py-3">Nama</th>
                    <th class="px-6 py-3">Email</th>
                    <th class="px-6 py-3">No. Telp</th>
                    <th class="px-6 py-3">Perusahaan</th>
                    <th class="px-6 py-3">Status</th>
                    <th class="px-6 py-3 text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse ($customers as $customer)
                    <tr>
                        <td class="px-6 py-4 font-medium text-gray-900">{{ $customer->name }}</td>
                        <td class="px-6 py-4">{{ $customer->email }}</td>
                        <td class="px-6 py-4">{{ $customer->phone ?? '-' }}</td>
                        <td class="px-6 py-4">{{ $customer->company ?? '-' }}</td>
                        <td class="px-6 py-4">
                            <span class="px-2 py-1 text-xs rounded-full bg-blue-100 text-blue-800 uppercase font-semibold">
                                {{ $customer->status }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-right space-x-2">
                            <a href="{{ route('customers.show', ['current_team' => auth()->user()->currentTeam->slug, 'id' => $customer->id]) }}" wire:navigate class="text-blue-600 hover:text-blue-900">Lihat</a>
                            <button wire:click="edit('{{ $customer->id }}')" class="text-indigo-600 hover:text-indigo-900">Edit</button>
                            
                            @if(auth()->user()->isTeamAdmin())
                                <button wire:click="delete('{{ $customer->id }}')" wire:confirm="Yakin ingin menghapus data ini?" class="text-red-600 hover:text-red-900">Hapus</button>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-6 py-4 text-center text-gray-400">Belum ada data pelanggan.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
        <div class="p-4">
            {{ $customers->links() }}
        </div>
    </div>

    <!-- Modal Form -->
    @if($isModalOpen)
    <div class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50">
        <div class="bg-white rounded-xl shadow-lg w-full max-w-md p-6 space-y-4">
            <h2 class="text-xl font-bold text-gray-800">{{ $customerId ? 'Edit Pelanggan' : 'Tambah Pelanggan' }}</h2>
            
            <form wire:submit.prevent="store" class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700">Nama</label>
                    <input type="text" wire:model="name" class="w-full mt-1 border rounded-lg p-2 text-sm">
                    @error('name') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Email</label>
                    <input type="email" wire:model="email" class="w-full mt-1 border rounded-lg p-2 text-sm">
                    @error('email') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">No. Telepon</label>
                    <input type="text" wire:model="phone" class="w-full mt-1 border rounded-lg p-2 text-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Perusahaan</label>
                    <input type="text" wire:model="company" class="w-full mt-1 border rounded-lg p-2 text-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Status</label>
                    <select wire:model="status" class="w-full mt-1 border rounded-lg p-2 text-sm">
                        <option value="lead">Lead</option>
                        <option value="prospect">Prospect</option>
                        <option value="customer">Customer</option>
                        <option value="churned">Churned</option>
                    </select>
                </div>

                <div class="flex justify-end space-x-2 pt-4">
                    <button type="button" wire:click="closeModal()" class="px-4 py-2 border rounded-lg text-sm text-gray-600">Batal</button>
                    <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-lg text-sm">Simpan</button>
                </div>
            </form>
        </div>
    </div>
    @endif
</div>