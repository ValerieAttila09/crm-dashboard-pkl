<?php

use Livewire\Component;

new class extends Component
{
    //
};
?>

<div>
<div class="bg-slate-50 dark:bg-zinc-950 min-h-screen text-zinc-800 dark:text-zinc-100 font-sans">

    <!-- 1. NAVBAR PUBLIK -->
    <nav class="sticky top-0 z-40 bg-white/80 dark:bg-zinc-900/80 backdrop-blur-md border-b border-zinc-200/80 dark:border-zinc-800 px-6 py-4 flex items-center justify-between">
        <div class="flex items-center gap-3">
            <div class="w-9 h-9 bg-indigo-600 rounded-xl flex items-center justify-center text-white font-black text-lg shadow-md shadow-indigo-500/20">
                P
            </div>
            <span class="font-bold text-lg text-zinc-900 dark:text-white tracking-tight">PropertyLiving</span>
        </div>

        <div class="hidden md:flex items-center gap-8 text-xs font-semibold text-zinc-600 dark:text-zinc-300">
            <a href="#hero" class="hover:text-indigo-600 dark:hover:text-indigo-400 transition">Beranda</a>
            <a href="#features" class="hover:text-indigo-600 dark:hover:text-indigo-400 transition">Keunggulan 360°</a>
            <a href="#units" class="hover:text-indigo-600 dark:hover:text-indigo-400 transition">Katalog Unit</a>
            <a href="#about" class="hover:text-indigo-600 dark:hover:text-indigo-400 transition">Tentang Kami</a>
        </div>

        <div class="flex items-center gap-3">
            <a href="{{ route('login') }}" class="px-4 py-2 text-xs font-bold text-zinc-700 dark:text-zinc-200 hover:text-indigo-600 transition">
                Masuk Admin
            </a>
            <a href="#units" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold rounded-xl shadow-md shadow-indigo-500/20 transition">
                Cari Unit 360°
            </a>
        </div>
    </nav>

    <!-- 2. HERO SECTION -->
    <section id="hero" class="relative pt-12 pb-20 px-6 max-w-7xl mx-auto grid grid-cols-1 lg:grid-cols-12 gap-12 items-center">
        <!-- Left Content -->
        <div class="lg:col-span-7 space-y-6">
            <span class="inline-flex items-center gap-2 px-3 py-1.5 bg-indigo-50 dark:bg-indigo-950/60 border border-indigo-200 dark:border-indigo-800/60 text-indigo-600 dark:text-indigo-400 rounded-full text-xs font-bold">
                ✨ Pengalaman Baru Jelajah Hunian
            </span>

            <h1 class="text-4xl sm:text-5xl lg:text-6xl font-black text-zinc-900 dark:text-white leading-[1.15] tracking-tight">
                Rasakan Sensasi Tur <span class="text-indigo-600 dark:text-indigo-400">Virtual 360°</span> Sebelum Sewa Unit
            </h1>

            <p class="text-zinc-500 dark:text-zinc-400 text-sm sm:text-base max-w-xl leading-relaxed">
                Jelajahi setiap sudut kamar, cek fasilitas lengkap, dan pastikan kenyamanan tempat tinggal masa depanmu secara mendalam langsung dari HP tanpa perlu survei lokasi fisik.
            </p>

            <!-- Search Quick Bar -->
            <div class="p-2 bg-white dark:bg-zinc-900 rounded-2xl shadow-xl shadow-zinc-200/50 dark:shadow-none border border-zinc-200/80 dark:border-zinc-800 grid grid-cols-1 sm:grid-cols-12 gap-2">
                <div class="sm:col-span-6 px-3 py-2">
                    <label class="block text-[10px] uppercase font-bold text-zinc-400">Lokasi / Gedung</label>
                    <input type="text" wire:model.live.debounce.300ms="searchLocation" placeholder="Ketik nama gedung atau kota..." class="w-full bg-transparent border-0 p-0 text-xs font-semibold text-zinc-800 dark:text-white focus:ring-0 placeholder-zinc-400">
                </div>
                <div class="sm:col-span-3 px-3 py-2 border-t sm:border-t-0 sm:border-l border-zinc-100 dark:border-zinc-800">
                    <label class="block text-[10px] uppercase font-bold text-zinc-400">Tipe Unit</label>
                    <select wire:model.live="selectedType" class="w-full bg-transparent border-0 p-0 text-xs font-semibold text-zinc-800 dark:text-white focus:ring-0 cursor-pointer">
                        <option value="">Semua Tipe</option>
                        <option value="Studio">Studio</option>
                        <option value="1 BR">1 BR</option>
                        <option value="2 BR">2 BR</option>
                    </select>
                </div>
                <div class="sm:col-span-3">
                    <a href="#units" class="w-full h-full min-h-[44px] bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl font-bold text-xs flex items-center justify-center shadow-md shadow-indigo-500/20 transition">
                        Cari Sekarang
                    </a>
                </div>
            </div>
        </div>

        <!-- Right Interactive Card Preview -->
        <div class="lg:col-span-5 relative">
            <div class="relative bg-zinc-900 rounded-3xl overflow-hidden shadow-2xl border border-zinc-800 group">
                <img src="https://images.unsplash.com/photo-1522708323590-d24dbb6b0267?auto=format&fit=crop&w=800&q=80" class="w-full h-[380px] object-cover opacity-80 group-hover:scale-105 transition duration-500" alt="360 Preview">
                <div class="absolute inset-0 bg-gradient-to-t from-zinc-950 via-transparent to-black/30"></div>
                
                <!-- Badge 360 Interactive Tag -->
                <div class="absolute top-4 left-4 px-3 py-1.5 bg-black/60 backdrop-blur-md border border-white/20 text-white text-xs font-bold rounded-full flex items-center gap-2">
                    <span class="w-2 h-2 bg-emerald-400 rounded-full animate-ping"></span>
                    Tur Virtual 360° Interactive
                </div>

                <div class="absolute bottom-6 left-6 right-6">
                    <p class="text-xs text-indigo-400 font-bold uppercase tracking-wider">Unit Rekomendasi</p>
                    <h3 class="text-xl font-bold text-white mt-1">Kamar Executive Studio A101</h3>
                    <p class="text-xs text-zinc-300 mt-1">Gedung Residence Park • Rp 2.500.000 / bln</p>
                </div>
            </div>
        </div>
    </section>

    <!-- 3. FEATURED UNITS LISTING -->
    <section id="units" class="py-16 px-6 max-w-7xl mx-auto">
        <div class="flex flex-col md:flex-row md:items-end justify-between gap-4 mb-10">
            <div>
                <h2 class="text-2xl sm:text-3xl font-black text-zinc-900 dark:text-white">Unit Kamar Siap Huni</h2>
                <p class="text-xs sm:text-sm text-zinc-500 mt-1">Jelajahi unit dengan dukungan foto panorama 360° interaktif.</p>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @forelse($featuredRooms as $room)
                <div class="bg-white dark:bg-zinc-900 rounded-2xl border border-zinc-200/80 dark:border-zinc-800 overflow-hidden shadow-sm hover:shadow-xl transition duration-300 flex flex-col justify-between">
                    <div>
                        <!-- Thumbnail / Panorama Preview -->
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

                        <!-- Card Info -->
                        <div class="p-5">
                            <span class="text-[10px] font-bold uppercase text-indigo-600 dark:text-indigo-400 tracking-wider">{{ $room->type }}</span>
                            <h3 class="text-base font-bold text-zinc-900 dark:text-white mt-0.5">Kamar {{ $room->room_number }}</h3>
                            <p class="text-xs text-zinc-500 truncate mt-1">🏢 {{ $room->property->name ?? 'Properti N/A' }}</p>
                            
                            <div class="mt-4 pt-3 border-t border-zinc-100 dark:border-zinc-800 flex items-center justify-between">
                                <div>
                                    <p class="text-[10px] text-zinc-400 font-semibold">Harga Sewa</p>
                                    <p class="text-sm font-black text-zinc-900 dark:text-white">Rp {{ number_format($room->price_per_month, 0, ',', '.') }}<span class="text-[10px] font-normal text-zinc-400">/bln</span></p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Card Actions -->
                    <div class="p-5 pt-0 flex gap-2">
                        <a href="{{ route('rooms.show', ['current_team' => $room->property->team->slug ?? 'default', 'roomNumber' => $room->room_number]) }}" 
                           class="flex-1 py-2.5 bg-indigo-50 hover:bg-indigo-100 dark:bg-indigo-950/50 dark:hover:bg-indigo-900/50 text-indigo-600 dark:text-indigo-300 text-center font-bold text-xs rounded-xl transition">
                            🌐 Tur 360°
                        </a>
                        <a href="https://wa.me/6281234567890?text={{ urlencode('Halo, saya tertarik untuk menyewa Kamar ' . $room->room_number . ' di ' . ($room->property->name ?? '')) }}" target="_blank"
                           class="px-4 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs rounded-xl shadow-md shadow-emerald-500/20 transition flex items-center justify-center">
                            💬 Tanya
                        </a>
                    </div>
                </div>
            @empty
                <div class="col-span-full py-12 text-center text-zinc-400 text-xs">
                    Belum ada unit kamar publik yang tersedia saat ini.
                </div>
            @endforelse
        </div>
    </section>

    <!-- 4. FOOTER -->
    <footer class="bg-white dark:bg-zinc-900 border-t border-zinc-200 dark:border-zinc-800 py-10 px-6">
        <div class="max-w-7xl mx-auto flex flex-col md:flex-row items-center justify-between gap-4 text-xs text-zinc-500">
            <p>© 2026 PropertyLiving Platform. All rights reserved.</p>
            <div class="flex gap-6">
                <a href="#" class="hover:text-zinc-800 dark:hover:text-white">Kebijakan Privasi</a>
                <a href="#" class="hover:text-zinc-800 dark:hover:text-white">Syarat & Ketentuan</a>
                <a href="#" class="hover:text-zinc-800 dark:hover:text-white">Kontak Admin</a>
            </div>
        </div>
    </footer>
</div>