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
            <a href="{{ route('properties') }}" class="hover:text-white transition">Katalog Unit</a>
            <a href="{{ route('about') }}" class="hover:text-white transition">Tentang Kami</a>
            <a href="{{ route('contact') }}" class="text-indigo-400 font-bold">Kontak</a>
        </div>

        <a href="{{ route('login') }}" class="px-4 py-2 bg-zinc-900 hover:bg-zinc-800 border border-zinc-800 text-white text-xs font-bold rounded-xl transition">
            Masuk Admin
        </a>
    </nav>

    <!-- CONTENT SECTION -->
    <section class="py-16 px-6 max-w-6xl mx-auto grid grid-cols-1 lg:grid-cols-12 gap-12 items-start">
        <!-- Left Info Side -->
        <div class="lg:col-span-5 space-y-6">
            <span class="px-3.5 py-1.5 rounded-full border border-indigo-500/30 bg-indigo-500/10 text-indigo-400 text-xs font-bold tracking-wide uppercase inline-block">
                💬 Hubungi Tim Kami
            </span>

            <h1 class="text-3xl sm:text-5xl font-black text-white tracking-tight leading-tight">
                Ada Pertanyaan atau Ingin Berkemimtraan?
            </h1>

            <p class="text-xs sm:text-sm text-zinc-400 leading-relaxed">
                Apakah Anda calon penyewa yang butuh bantuan informasi kamar, atau pemilik gedung yang ingin mendaftarkan properti ke dalam platform digital kami? Tim kami siap melayani Anda.
            </p>

            <div class="space-y-4 pt-4 border-t border-zinc-900">
                <div class="flex items-start gap-4">
                    <div class="w-10 h-10 rounded-xl bg-zinc-900 border border-zinc-800 flex items-center justify-center text-indigo-400 font-bold text-base shrink-0">📍</div>
                    <div>
                        <p class="text-xs font-bold text-white">Lokasi Kantor</p>
                        <p class="text-xs text-zinc-400 mt-0.5">Jl. Boulevard Utama No. 88, Jakarta Selatan</p>
                    </div>
                </div>

                <div class="flex items-start gap-4">
                    <div class="w-10 h-10 rounded-xl bg-zinc-900 border border-zinc-800 flex items-center justify-center text-emerald-400 font-bold text-base shrink-0">💬</div>
                    <div>
                        <p class="text-xs font-bold text-white">WhatsApp Fast Response</p>
                        <p class="text-xs text-zinc-400 mt-0.5">+62 812-3456-7890 (Senin - Minggu 08.00 - 20.00)</p>
                    </div>
                </div>

                <div class="flex items-start gap-4">
                    <div class="w-10 h-10 rounded-xl bg-zinc-900 border border-zinc-800 flex items-center justify-center text-purple-400 font-bold text-base shrink-0">✉️</div>
                    <div>
                        <p class="text-xs font-bold text-white">Email Kemitraan B2B</p>
                        <p class="text-xs text-zinc-400 mt-0.5">partner@propertyliving.com</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Form Side -->
        <div class="lg:col-span-7 bg-zinc-900/60 p-8 rounded-3xl border border-zinc-800/80 backdrop-blur-xl shadow-2xl">
            <h2 class="text-lg font-bold text-white mb-6">Kirim Pesan Instan</h2>

            @if (session()->has('message'))
                <div class="mb-4 p-3 bg-emerald-950/60 border border-emerald-800 text-emerald-300 rounded-xl text-xs font-semibold">
                    {{ session('message') }}
                </div>
            @endif

            <form wire:submit.prevent="submit" class="space-y-4 text-xs">
                <div>
                    <label class="block font-semibold mb-1 text-zinc-300">Nama Lengkap</label>
                    <input type="text" wire:model="name" placeholder="Masukkan nama Anda" class="w-full bg-zinc-950 border border-zinc-800 rounded-xl px-3.5 py-2.5 text-xs font-semibold text-white focus:border-indigo-500 focus:ring-0">
                    @error('name') <span class="text-red-400 text-[10px] mt-1 block">{{ $message }}</span> @enderror
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block font-semibold mb-1 text-zinc-300">Alamat Email</label>
                        <input type="email" wire:model="email" placeholder="nama@email.com" class="w-full bg-zinc-950 border border-zinc-800 rounded-xl px-3.5 py-2.5 text-xs font-semibold text-white focus:border-indigo-500 focus:ring-0">
                        @error('email') <span class="text-red-400 text-[10px] mt-1 block">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block font-semibold mb-1 text-zinc-300">Nomor WhatsApp</label>
                        <input type="text" wire:model="phone" placeholder="0812xxxx" class="w-full bg-zinc-950 border border-zinc-800 rounded-xl px-3.5 py-2.5 text-xs font-semibold text-white focus:border-indigo-500 focus:ring-0">
                        @error('phone') <span class="text-red-400 text-[10px] mt-1 block">{{ $message }}</span> @enderror
                    </div>
                </div>

                <div>
                    <label class="block font-semibold mb-1 text-zinc-300">Tujuan Pesan</label>
                    <select wire:model="subject" class="w-full bg-zinc-950 border border-zinc-800 rounded-xl px-3.5 py-2.5 text-xs font-semibold text-white focus:border-indigo-500 focus:ring-0 cursor-pointer">
                        <option value="Inquiry Sewa Unit">Inquiry Sewa Unit Kamar</option>
                        <option value="Kemitraan Pemilik Properti">Pendaftaran/Kemitraan Pemilik Gedung</option>
                        <option value="Kendala Teknis">Kendala Teknis / Masukan</option>
                    </select>
                </div>

                <div>
                    <label class="block font-semibold mb-1 text-zinc-300">Detail Pesan / Pertanyaan</label>
                    <textarea wire:model="message" rows="4" placeholder="Tuliskan pertanyaan Anda..." class="w-full bg-zinc-950 border border-zinc-800 rounded-xl px-3.5 py-2.5 text-xs font-semibold text-white focus:border-indigo-500 focus:ring-0"></textarea>
                    @error('message') <span class="text-red-400 text-[10px] mt-1 block">{{ $message }}</span> @enderror
                </div>

                <button type="submit" class="w-full py-3 bg-indigo-600 hover:bg-indigo-500 text-white font-bold text-xs rounded-xl shadow-lg shadow-indigo-600/30 transition">
                    Kirim Pesan via WhatsApp 💬
                </button>
            </form>
        </div>
    </section>

    <!-- FOOTER -->
    <footer class="bg-zinc-950 border-t border-zinc-900 py-10 px-6 text-xs text-zinc-600 text-center">
        <p>© 2026 PropertyLiving Platform. Engineered for Modern Living Experience.</p>
    </footer>
</div>