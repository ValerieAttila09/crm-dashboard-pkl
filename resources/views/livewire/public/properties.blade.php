<?php

use Livewire\Component;

new class extends Component
{
    //
};
?>

<div class="bg-zinc-950 text-zinc-100 min-h-screen font-sans selection:bg-indigo-500 selection:text-white relative">

    <!-- NAVBAR PUBLIK -->
    <nav class="sticky top-0 z-50 bg-zinc-950/80 backdrop-blur-xl border-b border-zinc-800/80 px-6 py-4 flex items-center justify-between">
        <a href="{{ route('home') }}" class="flex items-center gap-3">
            <div class="w-9 h-9 bg-gradient-to-br from-indigo-500 to-indigo-700 rounded-xl flex items-center justify-center text-white font-black text-lg shadow-lg shadow-indigo-500/30">
                P
            </div>
            <span class="font-extrabold text-lg tracking-tight text-white">PropertyLiving</span>
        </a>

        <div class="hidden md:flex items-center gap-8 text-xs font-semibold text-zinc-400">
            <a href="{{ route('home') }}" class="hover:text-white transition">Beranda</a>
            <a href="{{ route('properties') }}" class="text-indigo-400 font-bold">Katalog Unit</a>
            <a href="{{ route('about') }}" class="hover:text-white transition">Tentang Kami</a>
        </div>

        <a href="{{ route('login') }}" class="px-4 py-2 bg-zinc-900 hover:bg-zinc-800 border border-zinc-800 text-white text-xs font-bold rounded-xl transition">
            Masuk Admin
        </a>
    </nav>

    <!-- HEADER & SEARCH BAR -->
    <section class="pt-12 pb-8 px-6 max-w-7xl mx-auto">
        <div class="max-w-2xl mb-8">
            <h1 class="text-3xl sm:text-4xl font-black text-white tracking-tight">Katalog Unit Kamar Siap Huni</h1>
            <p class="text-xs sm:text-sm text-zinc-400 mt-2">Temukan hunian yang pas dengan tur panorama 360° sebelum melakukan pemesanan.</p>
        </div>

        <!-- FILTER PANEL -->
        <div class="p-4 rounded-2xl bg-zinc-900/80 border border-zinc-800 shadow-xl grid grid-cols-1 md:grid-cols-12 gap-4 items-center">
            <!-- Search Text -->
            <div class="md:col-span-4">
                <label class="block text-[10px] uppercase font-bold text-zinc-500 mb-1">Cari Kamar / Gedung</label>
                <input type="text" wire:model.live.debounce.300ms="search" placeholder="misal: Kamar A101 / Residence..." class="w-full bg-zinc-950 border border-zinc-800 rounded-xl px-3 py-2 text-xs font-semibold text-white focus:border-indigo-500 focus:ring-0">
            </div>

            <!-- Select Gedung -->
            <div class="md:col-span-3">
                <label class="block text-[10px] uppercase font-bold text-zinc-500 mb-1">Pilih Gedung</label>
                <select wire:model.live="propertyId" class="w-full bg-zinc-950 border border-zinc-800 rounded-xl px-3 py-2 text-xs font-semibold text-white focus:border-indigo-500 focus:ring-0 cursor-pointer">
                    <option value="">Semua Gedung</option>
                    @foreach($properties as $prop)
                        <option value="{{ $prop->id }}">{{ $prop->name }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Select Tipe -->
            <div class="md:col-span-3">
                <label class="block text-[10px] uppercase font-bold text-zinc-500 mb-1">Tipe Unit</label>
                <select wire:model.live="type" class="w-full bg-zinc-950 border border-zinc-800 rounded-xl px-3 py-2 text-xs font-semibold text-white focus:border-indigo-500 focus:ring-0 cursor-pointer">
                    <option value="">Semua Tipe</option>
                    <option value="Studio">Studio</option>
                    <option value="1 BR">1 BR</option>
                    <option value="2 BR">2 BR</option>
                </select>
            </div>

            <!-- Reset Button -->
            <div class="md:col-span-2 flex items-end">
                <button wire:click="resetFilters" class="w-full py-2 bg-zinc-800 hover:bg-zinc-700 text-zinc-300 font-bold text-xs rounded-xl transition">
                    Reset Filter
                </button>
            </div>
        </div>
    </section>

    <!-- GRID KATALOG UNIT -->
    <section class="pb-20 px-6 max-w-7xl mx-auto">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @forelse($rooms as $room)
                <div class="bg-zinc-900/60 rounded-2xl border border-zinc-800/80 overflow-hidden shadow-sm hover:border-indigo-500/50 transition duration-300 flex flex-col justify-between">
                    <div>
                        <!-- Thumbnail Preview -->
                        <div class="relative h-48 bg-zinc-800 overflow-hidden">
                            <img src="{{ $room->panorama_360_url ?? 'https://images.unsplash.com/photo-1502672260266-1c1ef2d93688?auto=format&fit=crop&w=600&q=80' }}" class="w-full h-full object-cover" alt="Kamar {{ $room->room_number }}">
                            <span class="absolute top-3 right-3 px-2.5 py-1 bg-emerald-500 text-white font-extrabold text-[10px] uppercase rounded-md shadow">
                                AVAILABLE
                            </span>
                            @if($room->scenes->count() > 0)
                                <span class="absolute bottom-3 left-3 px-2.5 py-1 bg-black/60 backdrop-blur-md text-white font-bold text-[10px] rounded-md flex items-center gap-1">
                                    🌐 {{ $room->scenes->count() }} Scene 360°
                                </span>
                            @endif
                        </div>

                        <!-- Info Unit -->
                        <div class="p-5">
                            <span class="text-[10px] font-bold uppercase text-indigo-400 tracking-wider">{{ $room->type }}</span>
                            <h3 class="text-base font-bold text-white mt-0.5">Kamar {{ $room->room_number }}</h3>
                            <p class="text-xs text-zinc-400 truncate mt-1">🏢 {{ $room->property->name ?? 'Properti N/A' }}</p>
                            
                            <div class="mt-4 pt-3 border-t border-zinc-800 flex items-center justify-between">
                                <div>
                                    <p class="text-[10px] text-zinc-500 font-semibold">Harga Sewa</p>
                                    <p class="text-sm font-black text-white">Rp {{ number_format($room->price_per_month, 0, ',', '.') }}<span class="text-[10px] font-normal text-zinc-500">/bln</span></p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="p-5 pt-0 flex gap-2">
                        <a href="{{ route('rooms.show', ['current_team' => $room->property->team->slug ?? 'default', 'roomNumber' => $room->room_number]) }}" 
                           class="flex-1 py-2.5 bg-indigo-950/50 hover:bg-indigo-900/50 text-indigo-300 text-center font-bold text-xs rounded-xl border border-indigo-800/40 transition">
                            🌐 Tur 360°
                        </a>
                        <a href="https://wa.me/6281234567890?text={{ urlencode('Halo, saya tertarik untuk menyewa Kamar ' . $room->room_number . ' di ' . ($room->property->name ?? '')) }}" target="_blank"
                           class="px-4 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs rounded-xl shadow-md shadow-emerald-500/20 transition flex items-center justify-center">
                            💬 Tanya
                        </a>
                    </div>
                </div>
            @empty
                <div class="col-span-full py-16 text-center text-zinc-500 text-xs">
                    Tidak ada unit kamar yang sesuai dengan kriteria pencarian Anda.
                </div>
            @endforelse
        </div>

        <!-- PAGINATION -->
        <div class="mt-8">
            {{ $rooms->links() }}
        </div>
    </section>

    <!-- FOOTER -->
    <footer class="bg-zinc-950 border-t border-zinc-900 py-10 px-6 text-xs text-zinc-600 text-center">
        <p>© 2026 PropertyLiving Platform. Engineered for Modern Living Experience.</p>
    </footer>
</div>