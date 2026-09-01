<?php

use Livewire\Component;

new class extends Component
{
    //
};
?>

<div class="p-6">
    <!-- Load Pannellum 360 Viewer CDN -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/pannellum@2.5.6/build/pannellum.css"/>
    <script src="https://cdn.jsdelivr.net/npm/pannellum@2.5.6/build/pannellum.js"></script>

    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Manajemen Kamar & Unit</h1>
            <p class="text-xs text-gray-500 dark:text-gray-400">Kelola unit kamar, tarif sewa, dan tampilan 360° panorama.</p>
        </div>
        <button wire:click="create" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-semibold rounded-lg shadow">
            + Tambah Kamar
        </button>
    </div>

    <!-- Filter Bar -->
    <div class="flex flex-col md:flex-row gap-3 mb-6">
        <input type="text" wire:model.live.debounce.300ms="search" placeholder="Cari nomor kamar / tipe..." class="px-3 py-2 border rounded-lg text-xs dark:bg-zinc-800 dark:border-zinc-700 dark:text-white flex-1">
        <select wire:model.live="propertyFilter" class="px-3 py-2 border rounded-lg text-xs dark:bg-zinc-800 dark:border-zinc-700 dark:text-white">
            <option value="">Semua Properti</option>
            @foreach($properties as $prop)
                <option value="{{ $prop->id }}">{{ $prop->name }}</option>
            @endforeach
        </select>
        <select wire:model.live="statusFilter" class="px-3 py-2 border rounded-lg text-xs dark:bg-zinc-800 dark:border-zinc-700 dark:text-white">
            <option value="">Semua Status</option>
            <option value="available">Tersedia</option>
            <option value="occupied">Terisi</option>
            <option value="maintenance">Perawatan</option>
        </select>
    </div>

    <!-- Grid Card Unit Kamar -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse($rooms as $room)
            <div class="bg-white dark:bg-zinc-800 rounded-xl border border-gray-200 dark:border-zinc-700 overflow-hidden shadow-sm flex flex-col justify-between">
                <div>
                    <!-- Container 360 View Preview / Placeholder -->
                    <div class="relative h-48 bg-zinc-900 flex items-center justify-center">
                        @if($room->panorama_360_url)
                            <div id="panorama-{{ $room->id }}" class="w-full h-full"></div>
                            <script>
                                document.addEventListener("DOMContentLoaded", function() {
                                    pannellum.viewer('panorama-{{ $room->id }}', {
                                        "type": "equirectangular",
                                        "panorama": "{{ $room->panorama_360_url }}",
                                        "autoLoad": true,
                                        "showControls": false
                                    });
                                });
                            </script>
                            <span class="absolute top-2 left-2 bg-black/60 text-white text-[10px] px-2 py-0.5 rounded-full font-bold">360° View</span>
                        @else
                            <span class="text-xs text-zinc-500">Foto 360° Belum Diunggah</span>
                        @endif
                    </div>

                    <div class="p-4">
                        <div class="flex justify-between items-start mb-2">
                            <div>
                                <h3 class="font-bold text-gray-900 dark:text-white text-base">Kamar {{ $room->room_number }}</h3>
                                <p class="text-xs text-gray-500 dark:text-gray-400">{{ $room->property->name ?? 'Tanpa Properti' }} • {{ $room->type }}</p>
                            </div>
                            <span class="text-[10px] font-bold uppercase px-2 py-0.5 rounded-full 
                                {{ $room->status === 'available' ? 'bg-emerald-100 text-emerald-800' : ($room->status === 'occupied' ? 'bg-indigo-100 text-indigo-800' : 'bg-amber-100 text-amber-800') }}">
                                {{ $room->status }}
                            </span>
                        </div>

                        <p class="text-lg font-extrabold text-indigo-600 dark:text-indigo-400">
                            Rp {{ number_format($room->price_per_month, 0, ',', '.') }} <span class="text-xs font-normal text-gray-400">/ bulan</span>
                        </p>
                    </div>
                </div>

                <div class="p-4 border-t border-gray-100 dark:border-zinc-700/50 flex justify-end gap-2 bg-gray-50 dark:bg-zinc-900/50">
                    <button wire:click="edit('{{ $room->id }}')" class="px-3 py-1.5 bg-gray-200 dark:bg-zinc-700 hover:bg-gray-300 text-xs font-semibold rounded text-gray-800 dark:text-gray-200">
                        Edit
                    </button>
                </div>
            </div>
        @empty
            <div class="col-span-full text-center py-12 text-sm text-gray-400">
                Belum ada unit kamar. Klik tombol "+ Tambah Kamar" untuk memulai.
            </div>
        @endforelse
    </div>

    <!-- Modal Form Tambah/Edit -->
    @if($isModalOpen)
        <div class="fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4">
            <div class="bg-white dark:bg-zinc-800 rounded-xl p-6 w-full max-w-lg shadow-xl border border-zinc-700">
                <h2 class="text-lg font-bold text-gray-900 dark:text-white mb-4">{{ $roomId ? 'Edit Kamar' : 'Tambah Kamar Baru' }}</h2>

                <div class="space-y-3 text-xs">
                    <div>
                        <label class="block font-semibold mb-1 dark:text-gray-300">Pilih Properti</label>
                        <select wire:model="property_id" class="w-full p-2 border rounded dark:bg-zinc-900 dark:border-zinc-700 dark:text-white">
                            <option value="">-- Pilih Properti --</option>
                            @foreach($properties as $p)
                                <option value="{{ $p->id }}">{{ $p->name }}</option>
                            @endforeach
                        </select>
                        @error('property_id') <span class="text-red-500 text-[10px]">{{ $message }}</span> @enderror
                    </div>

                    <div class="grid grid-cols-2 gap-2">
                        <div>
                            <label class="block font-semibold mb-1 dark:text-gray-300">Nomor / Kode Kamar</label>
                            <input type="text" wire:model="room_number" placeholder="misal: A-101" class="w-full p-2 border rounded dark:bg-zinc-900 dark:border-zinc-700 dark:text-white">
                            @error('room_number') <span class="text-red-500 text-[10px]">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="block font-semibold mb-1 dark:text-gray-300">Tipe Kamar</label>
                            <input type="text" wire:model="type" placeholder="Studio, Deluxe, 1BR..." class="w-full p-2 border rounded dark:bg-zinc-900 dark:border-zinc-700 dark:text-white">
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-2">
                        <div>
                            <label class="block font-semibold mb-1 dark:text-gray-300">Harga Sewa / Bulan (Rp)</label>
                            <input type="number" wire:model="price_per_month" class="w-full p-2 border rounded dark:bg-zinc-900 dark:border-zinc-700 dark:text-white">
                        </div>
                        <div>
                            <label class="block font-semibold mb-1 dark:text-gray-300">Status</label>
                            <select wire:model="status" class="w-full p-2 border rounded dark:bg-zinc-900 dark:border-zinc-700 dark:text-white">
                                <option value="available">Tersedia</option>
                                <option value="occupied">Terisi</option>
                                <option value="maintenance">Perawatan</option>
                            </select>
                        </div>
                    </div>

                    <div>
                        <label class="block font-semibold mb-1 dark:text-gray-300">URL Foto Panorama 360°</label>
                        <input type="url" wire:model="panorama_360_url" placeholder="https://domain.com/foto-360.jpg" class="w-full p-2 border rounded dark:bg-zinc-900 dark:border-zinc-700 dark:text-white">
                        <span class="text-[10px] text-gray-400">Masukkan URL link gambar equirectangular 360 derajat.</span>
                    </div>
                </div>

                <div class="flex justify-end gap-2 mt-6">
                    <button wire:click="closeModal" class="px-4 py-2 bg-gray-200 text-xs font-semibold rounded">Batal</button>
                    <button wire:click="store" class="px-4 py-2 bg-indigo-600 text-white text-xs font-semibold rounded">Simpan</button>
                </div>
            </div>
        </div>
    @endif
</div>