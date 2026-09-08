<?php

use Livewire\Component;

new class extends Component
{
    //
};
?>

<div class="bg-zinc-950 text-zinc-100 min-h-screen font-sans selection:bg-indigo-500 selection:text-white relative overflow-hidden">

    <!-- Glowing Background Gradients (Aura FX) -->
    <div class="absolute top-0 left-1/2 -translate-x-1/2 w-[1000px] h-[400px] bg-gradient-to-tr from-indigo-600/30 via-purple-600/20 to-emerald-500/10 blur-[120px] pointer-events-none -z-10 rounded-full"></div>
    <div class="absolute bottom-1/3 left-0 w-[500px] h-[500px] bg-indigo-900/20 blur-[150px] pointer-events-none -z-10"></div>

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
            <a href="{{ route('home') }}#units" class="hover:text-white transition">Katalog Unit</a>
            <a href="{{ route('about') }}" class="text-indigo-400 font-bold">Tentang Kami</a>
        </div>

        <a href="https://wa.me/6281234567890" target="_blank" class="px-4 py-2 bg-gradient-to-r from-indigo-600 to-indigo-500 hover:from-indigo-500 hover:to-indigo-400 text-white text-xs font-bold rounded-xl shadow-lg shadow-indigo-500/25 transition-all transform hover:-translate-y-0.5">
            Konsultasi Kemitraan
        </a>
    </nav>

    <!-- 1. HERO STORY SECTION -->
    <section class="pt-20 pb-16 px-6 max-w-5xl mx-auto text-center relative">
        <span class="px-3.5 py-1.5 rounded-full border border-indigo-500/30 bg-indigo-500/10 text-indigo-400 text-xs font-bold tracking-wide uppercase inline-block mb-6 shadow-sm">
            ✨ Redefining Real Estate & Living Experience
        </span>

        <h1 class="text-4xl sm:text-6xl font-black tracking-tight leading-[1.1] text-transparent bg-clip-text bg-gradient-to-r from-white via-zinc-200 to-zinc-400 mb-6">
            Masa Depan Memilih & Mengelola Property Tinggal Ada di Sini.
        </h1>

        <p class="text-zinc-400 text-sm sm:text-base max-w-2xl mx-auto leading-relaxed">
            PropertyLiving hadir menjembatani calon penghuni dengan ruang hidup impiannya melalui integrasi teknologi <span class="text-white font-semibold">Virtual Tour 360°</span>, efisiensi manajemen sewa otomatis, dan transparansi penuh.
        </p>
    </section>

    <!-- 2. REAL-TIME METRICS BENTO GRID -->
    <section class="py-10 px-6 max-w-6xl mx-auto">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <!-- Metric 1 -->
            <div class="p-8 rounded-3xl bg-gradient-to-b from-zinc-900/90 to-zinc-900/40 border border-zinc-800/80 backdrop-blur-lg relative overflow-hidden group hover:border-indigo-500/50 transition duration-500">
                <div class="absolute -right-6 -bottom-6 w-24 h-24 bg-indigo-500/10 rounded-full blur-2xl group-hover:bg-indigo-500/20 transition"></div>
                <p class="text-xs font-bold text-zinc-500 uppercase tracking-widest">Gedung & Properti</p>
                <h2 class="text-4xl sm:text-5xl font-black text-white mt-3">{{ $totalProperties }} <span class="text-indigo-500 text-2xl">+</span></h2>
                <p class="text-xs text-zinc-400 mt-2">Lokasi strategis yang dikelola terintegrasi dalam sistem.</p>
            </div>

            <!-- Metric 2 -->
            <div class="p-8 rounded-3xl bg-gradient-to-b from-zinc-900/90 to-zinc-900/40 border border-zinc-800/80 backdrop-blur-lg relative overflow-hidden group hover:border-indigo-500/50 transition duration-500">
                <div class="absolute -right-6 -bottom-6 w-24 h-24 bg-emerald-500/10 rounded-full blur-2xl group-hover:bg-emerald-500/20 transition"></div>
                <p class="text-xs font-bold text-zinc-500 uppercase tracking-widest">Total Unit Kamar</p>
                <h2 class="text-4xl sm:text-5xl font-black text-white mt-3">{{ $totalRooms }} <span class="text-emerald-400 text-2xl">Unit</span></h2>
                <p class="text-xs text-zinc-400 mt-2">Pilihan unit modern dengan tur panorama 360°.</p>
            </div>

            <!-- Metric 3 -->
            <div class="p-8 rounded-3xl bg-gradient-to-b from-zinc-900/90 to-zinc-900/40 border border-zinc-800/80 backdrop-blur-lg relative overflow-hidden group hover:border-indigo-500/50 transition duration-500">
                <div class="absolute -right-6 -bottom-6 w-24 h-24 bg-purple-500/10 rounded-full blur-2xl group-hover:bg-purple-500/20 transition"></div>
                <p class="text-xs font-bold text-zinc-500 uppercase tracking-widest">Tingkat Okupansi</p>
                <h2 class="text-4xl sm:text-5xl font-black text-white mt-3">{{ $occupancyRate }}<span class="text-purple-400 text-2xl">%</span></h2>
                <p class="text-xs text-zinc-400 mt-2">Kepercayaan tinggi dari penghuni dan investor properti.</p>
            </div>
        </div>
    </section>

    <!-- 3. CORE PILLARS / ADVANTAGES (CREATIVE CARDS) -->
    <section class="py-20 px-6 max-w-6xl mx-auto">
        <div class="text-center max-w-xl mx-auto mb-16">
            <h2 class="text-2xl sm:text-4xl font-black text-white">Mengapa Memilih Platform Kami?</h2>
            <p class="text-xs sm:text-sm text-zinc-400 mt-2">Dibuat untuk memudahkan calon penyewa dan menyederhanakan tugas pemilik properti.</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            <!-- Feature 1 -->
            <div class="p-6 rounded-2xl bg-zinc-900/60 border border-zinc-800 hover:border-zinc-700 transition space-y-4">
                <div class="w-12 h-12 bg-indigo-500/10 border border-indigo-500/30 rounded-xl flex items-center justify-center text-indigo-400 font-bold text-xl">
                    🌐
                </div>
                <h3 class="text-base font-bold text-white">Immersive 360° Virtual Tour</h3>
                <p class="text-xs text-zinc-400 leading-relaxed">
                    Lihat kondisi kamar secara mendetail hingga setiap sudut tanpa perlu melakukan perjalanan fisik. Hemat waktu, akurat, dan transparan.
                </p>
            </div>

            <!-- Feature 2 -->
            <div class="p-6 rounded-2xl bg-zinc-900/60 border border-zinc-800 hover:border-zinc-700 transition space-y-4">
                <div class="w-12 h-12 bg-emerald-500/10 border border-emerald-500/30 rounded-xl flex items-center justify-center text-emerald-400 font-bold text-xl">
                    ⚡
                </div>
                <h3 class="text-base font-bold text-white">Instant Booking & WhatsApp Sync</h3>
                <p class="text-xs text-zinc-400 leading-relaxed">
                    Proses pemesanan unit dan konfirmasi tagihan sewa bulanan langsung terhubung ke pengelola via WhatsApp secara instant.
                </p>
            </div>

            <!-- Feature 3 -->
            <div class="p-6 rounded-2xl bg-zinc-900/60 border border-zinc-800 hover:border-zinc-700 transition space-y-4">
                <div class="w-12 h-12 bg-purple-500/10 border border-purple-500/30 rounded-xl flex items-center justify-center text-purple-400 font-bold text-xl">
                    🛠️
                </div>
                <h3 class="text-base font-bold text-white">Smart Maintenance Ticketing</h3>
                <p class="text-xs text-zinc-400 leading-relaxed">
                    Sistem pelaporan kerusakan fasilitas yang cepat, transparan, dan dapat dipantau status pengerjaannya oleh penghuni.
                </p>
            </div>
        </div>
    </section>

    <!-- 4. CALL TO ACTION (CTA BANNER) -->
    <section class="py-16 px-6 max-w-5xl mx-auto">
        <div class="p-10 sm:p-14 rounded-3xl bg-gradient-to-r from-indigo-900/80 via-indigo-950 to-zinc-900 border border-indigo-500/30 relative overflow-hidden text-center space-y-6">
            <div class="absolute -right-10 -top-10 w-48 h-48 bg-indigo-500/20 rounded-full blur-3xl pointer-events-none"></div>
            
            <h2 class="text-2xl sm:text-4xl font-black text-white tracking-tight">Siap Menemukan Kamar Impianmu?</h2>
            <p class="text-xs sm:text-sm text-zinc-300 max-w-lg mx-auto">
                Coba pengalaman survei hunian secara virtual sekarang juga atau hubungi tim kami untuk konsultasi sewa.
            </p>

            <div class="pt-2 flex flex-col sm:flex-row justify-center gap-4">
                <a href="{{ route('home') }}#units" class="px-6 py-3 bg-white text-zinc-900 font-bold text-xs rounded-xl hover:bg-zinc-100 transition shadow-lg">
                    Jelajahi Katalog 360°
                </a>
                <a href="https://wa.me/6281234567890" target="_blank" class="px-6 py-3 bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs rounded-xl transition shadow-lg shadow-indigo-600/30">
                    Hubungi Admin Sales
                </a>
            </div>
        </div>
    </section>

    <!-- FOOTER -->
    <footer class="bg-zinc-950 border-t border-zinc-900 py-10 px-6 text-xs text-zinc-600 text-center">
        <p>© 2026 PropertyLiving Platform. Engineered for Modern Living Experience.</p>
    </footer>
</div>