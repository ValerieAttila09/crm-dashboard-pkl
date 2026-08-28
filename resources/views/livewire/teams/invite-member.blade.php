<?php

use Livewire\Component;

new class extends Component
{
    //
};
?>

<div>
    <!-- Tombol Pemicu Modal (Hanya muncul untuk Admin) -->
    @if(auth()->user()->isTeamAdmin())
        <button wire:click="openModal" class="px-3 py-1.5 bg-indigo-600 text-white text-xs font-semibold rounded-lg hover:bg-indigo-700 transition flex items-center gap-1.5">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path></svg>
            + Undang Anggota
        </button>
    @endif

    <!-- Flash Message Modal Trigger Notif -->
    @if (session()->has('message'))
        <div class="fixed bottom-4 right-4 z-50 p-4 bg-emerald-600 text-white text-xs font-medium rounded-lg shadow-lg">
            {{ session('message') }}
        </div>
    @endif

    @if (session()->has('error'))
        <div class="fixed bottom-4 right-4 z-50 p-4 bg-red-600 text-white text-xs font-medium rounded-lg shadow-lg">
            {{ session('error') }}
        </div>
    @endif

    <!-- Modal Form Invite -->
    @if($isModalOpen)
    <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm">
        <div class="bg-white dark:bg-zinc-800 rounded-xl shadow-xl w-full max-w-md p-6 space-y-4 border border-gray-100 dark:border-zinc-700">
            <div class="flex justify-between items-center border-b dark:border-zinc-700 pb-3">
                <h3 class="text-lg font-bold text-gray-800 dark:text-white">Undang Anggota Tim</h3>
                <button wire:click="closeModal" class="text-gray-400 hover:text-gray-600 dark:hover:text-white">&times;</button>
            </div>
            
            <form wire:submit.prevent="invite" class="space-y-4">
                <div>
                    <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Email Pengguna</label>
                    <input type="email" wire:model="email" placeholder="contoh: rekan@perusahaan.com" class="w-full border dark:border-zinc-700 rounded-lg p-2.5 text-xs dark:bg-zinc-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    @error('email') <span class="text-red-500 text-[11px] mt-1 block">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Peran (Role) dalam Tim</label>
                    <select wire:model="role" class="w-full border dark:border-zinc-700 rounded-lg p-2.5 text-xs dark:bg-zinc-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        <option value="member">Member / Sales (Akses Operasional)</option>
                        <option value="admin">Admin (Akses Penuh & Hapus Data)</option>
                    </select>
                    @error('role') <span class="text-red-500 text-[11px] mt-1 block">{{ $message }}</span> @enderror
                </div>

                <div class="flex justify-end space-x-2 pt-4 border-t dark:border-zinc-700">
                    <button type="button" wire:click="closeModal" class="px-4 py-2 border rounded-lg text-xs font-medium text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-zinc-700">Batal</button>
                    <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-lg text-xs font-medium hover:bg-indigo-700 transition">Tambahkan ke Tim</button>
                </div>
            </form>
        </div>
    </div>
    @endif
</div>