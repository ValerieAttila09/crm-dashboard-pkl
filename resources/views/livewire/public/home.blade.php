<?php

use Livewire\Component;

new class extends Component
{
    //
};
?>

<div class="bg-white text-zinc-900 font-sans antialiased selection:bg-orange-500 selection:text-white">

    <!-- 1. FLOATING GLASS NAVBAR -->
    <header class="fixed top-4 inset-x-0 z-50 max-w-7xl mx-auto px-4">
        <nav class="bg-zinc-900/90 backdrop-blur-md text-white rounded-full px-6 py-3.5 flex items-center justify-between border border-white/10 shadow-2xl">
            <div class="flex items-center gap-3">
                <div class="w-8 h-8 bg-orange-500 rounded-full flex items-center justify-center font-black text-sm text-white">
                    P
                </div>
                <span class="font-extrabold tracking-tight text-sm">PropertyLiving</span>
            </div>

            <div class="hidden md:flex items-center gap-8 text-xs font-medium text-zinc-300">
                <a href="#hero" class="hover:text-orange-400 transition">Home</a>
                <a href="#about" class="hover:text-orange-400 transition">About Us</a>
                <a href="#units" class="hover:text-orange-400 transition">Living Spaces</a>
                <a href="#insights" class="hover:text-orange-400 transition">Virtual 360°</a>
            </div>

            <div class="flex items-center gap-3">
                <a href="{{ route('login') }}" class="px-4 py-2 text-xs font-semibold hover:text-orange-400 transition">Login</a>
                <a href="#contact" class="px-5 py-2 bg-orange-500 hover:bg-orange-600 text-white text-xs font-bold rounded-full transition shadow-lg shadow-orange-500/30">
                    Contact Us
                </a>
            </div>
        </nav>
    </header>

    <!-- 2. FULL-BLEED HERO BANNER -->
    <section id="hero" class="relative min-h-[85vh] flex items-end pb-16 px-6 pt-32">
        <div class="absolute inset-0 z-0">
            <img src="https://images.unsplash.com/photo-1600585154340-be6161a56a0c?auto=format&fit=crop&w=1800&q=80" 
                 class="w-full h-full object-cover brightness-[0.45]" alt="Hero Living Space">
            <div class="absolute inset-0 bg-gradient-to-t from-zinc-950/80 via-transparent to-black/30"></div>
        </div>

        <div class="relative z-10 max-w-7xl mx-auto w-full grid grid-cols-1 lg:grid-cols-12 gap-8 items-end">
            <div class="lg:col-span-8 space-y-4">
                <h1 class="text-4xl sm:text-6xl lg:text-7xl font-bold text-white tracking-tight leading-none">
                    We provide fully <br/><span class="text-zinc-300 font-light italic">immersive living.</span>
                </h1>
            </div>
            <div class="lg:col-span-4 space-y-4">
                <p class="text-zinc-300 text-xs sm:text-sm leading-relaxed">
                    Eksplorasi hunian modern dan fasilitas apartemen secara mendalam melalui teknologi Virtual Tour 360° sebelum menentukan pilihan sewa Anda.
                </p>
                <a href="#units" class="inline-flex items-center gap-2 px-6 py-3 bg-orange-500 hover:bg-orange-600 text-white font-bold text-xs rounded-full shadow-xl transition">
                    Explore Spaces &rarr;
                </a>
            </div>
        </div>
    </section>

    <!-- 3. INTRO SECTION & IMAGE GALLERY -->
    <section id="about" class="py-24 px-6 max-w-7xl mx-auto text-center">
        <span class="px-3 py-1 bg-orange-100 text-orange-600 text-[10px] font-bold uppercase tracking-widest rounded-full">
            Modern Living Experience
        </span>

        <h2 class="text-3xl sm:text-5xl font-extrabold tracking-tight text-zinc-900 mt-4 max-w-3xl mx-auto leading-tight">
            Integrated Living & Space Management Solutions
        </h2>

        <p class="text-zinc-500 text-xs sm:text-sm max-w-2xl mx-auto mt-4 leading-relaxed">
            Menghadirkan standar baru pengelolaan properti tempat tinggal dengan transparansi tagihan sewa, respon cepat perbaikan, dan kebebasan inspeksi unit berbasis digital.
        </p>

        <div class="mt-8">
            <a href="{{ route('about') }}" class="px-6 py-2.5 bg-orange-500 hover:bg-orange-600 text-white text-xs font-bold rounded-full transition inline-block">
                Read More &rarr;
            </a>
        </div>

        <!-- 4-Column Image Grid -->
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mt-16">
            <div class="h-64 rounded-2xl overflow-hidden shadow-md">
                <img src="https://images.unsplash.com/photo-1522708323590-d24dbb6b0267?auto=format&fit=crop&w=600&q=80" class="w-full h-full object-cover hover:scale-105 transition duration-500" alt="Space 1">
            </div>
            <div class="h-64 rounded-2xl overflow-hidden shadow-md">
                <img src="https://images.unsplash.com/photo-1502672260266-1c1ef2d93688?auto=format&fit=crop&w=600&q=80" class="w-full h-full object-cover hover:scale-105 transition duration-500" alt="Space 2">
            </div>
            <div class="h-64 rounded-2xl overflow-hidden shadow-md">
                <img src="https://images.unsplash.com/photo-1560448204-e02f11c3d0e2?auto=format&fit=crop&w=600&q=80" class="w-full h-full object-cover hover:scale-105 transition duration-500" alt="Space 3">
            </div>
            <div class="h-64 rounded-2xl overflow-hidden shadow-md">
                <img src="https://images.unsplash.com/photo-1512918728675-ed5a9ecdebfd?auto=format&fit=crop&w=600&q=80" class="w-full h-full object-cover hover:scale-105 transition duration-500" alt="Space 4">
            </div>
        </div>
    </section>

    <!-- 4. DARK CONTRAST FEATURE CONTAINER (Seperti referensi hitam di tengah) -->
    <section class="py-16 px-4 max-w-7xl mx-auto">
        <div class="bg-zinc-900 rounded-3xl p-8 sm:p-16 text-white text-center space-y-12">
            <div class="max-w-2xl mx-auto space-y-4">
                <span class="px-3 py-1 bg-orange-500/20 text-orange-400 text-[10px] font-bold uppercase tracking-widest rounded-full">
                    Our Commitment
                </span>
                <h2 class="text-2xl sm:text-4xl font-extrabold leading-snug">
                    We create long-term relationships with every tenant by managing spaces completely.
                </h2>
                <a href="{{ route('properties') }}" class="inline-block px-6 py-2.5 bg-orange-500 hover:bg-orange-600 text-white text-xs font-bold rounded-full transition">
                    View Catalog
                </a>
            </div>

            <!-- Dynamic Room Showcase Cards -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 text-left">
                @forelse($featuredRooms as $room)
                    <div class="bg-zinc-800/80 rounded-2xl p-3 border border-zinc-700/60 hover:border-orange-500/50 transition">
                        <div class="h-40 rounded-xl overflow-hidden relative">
                            <img src="{{ $room->panorama_360_url ?? 'https://images.unsplash.com/photo-1502672260266-1c1ef2d93688?auto=format&fit=crop&w=600&q=80' }}" class="w-full h-full object-cover" alt="Kamar {{ $room->room_number }}">
                            <span class="absolute top-2 right-2 px-2 py-0.5 bg-orange-500 text-white font-black text-[9px] uppercase rounded">
                                360° TOUR
                            </span>
                        </div>
                        <div class="mt-3 px-1">
                            <h3 class="text-sm font-bold text-white">Kamar {{ $room->room_number }}</h3>
                            <p class="text-xs text-zinc-400 mt-0.5">Rp {{ number_format($room->price_per_month, 0, ',', '.') }} / bln</p>
                        </div>
                    </div>
                @empty
                    <div class="col-span-full text-center text-zinc-500 text-xs py-6">
                        Belum ada kamar unggulan yang ditampilkan.
                    </div>
                @endforelse
            </div>
        </div>
    </section>

    <!-- 5. CALL TO ACTION BANNER (ORANGE CARD) -->
    <section class="py-16 px-6 max-w-7xl mx-auto">
        <div class="bg-gradient-to-r from-orange-500 to-orange-600 rounded-3xl p-10 sm:p-14 text-center text-white space-y-6 shadow-2xl shadow-orange-500/20">
            <h2 class="text-3xl sm:text-5xl font-black tracking-tight">Let's Discuss Your Next Space</h2>
            <p class="text-xs sm:text-sm text-orange-100 max-w-lg mx-auto">
                Apakah Anda mencari unit tempat tinggal baru atau ingin mendaftarkan gedung Anda ke dalam manajemen kami?
            </p>
            <div class="pt-2 flex justify-center gap-3">
                <a href="{{ route('contact') }}" class="px-6 py-3 bg-white text-orange-600 font-bold text-xs rounded-full hover:bg-zinc-100 transition shadow">
                    Contact Us
                </a>
                <a href="https://wa.me/6281234567890" target="_blank" class="px-6 py-3 bg-zinc-900 text-white font-bold text-xs rounded-full hover:bg-black transition shadow">
                    WhatsApp Direct
                </a>
            </div>
        </div>
    </section>

    <!-- 6. FOOTER -->
    <footer class="bg-white border-t border-zinc-200 py-12 px-6">
        <div class="max-w-7xl mx-auto grid grid-cols-1 md:grid-cols-4 gap-8 text-xs text-zinc-500">
            <div class="space-y-3">
                <div class="flex items-center gap-2 text-zinc-900 font-bold">
                    <div class="w-6 h-6 bg-orange-500 rounded-full flex items-center justify-center text-white text-xs">P</div>
                    <span>PropertyLiving</span>
                </div>
                <p class="text-[11px] leading-relaxed">Platform manajemen hunian modern berbasis analisis IoT dan tur virtual 360° interaktif.</p>
            </div>
            <div>
                <h4 class="font-bold text-zinc-900 mb-3 uppercase text-[10px] tracking-wider">Company</h4>
                <ul class="space-y-2">
                    <li><a href="{{ route('about') }}" class="hover:text-zinc-900">About Us</a></li>
                    <li><a href="{{ route('properties') }}" class="hover:text-zinc-900">Living Catalog</a></li>
                </ul>
            </div>
            <div>
                <h4 class="font-bold text-zinc-900 mb-3 uppercase text-[10px] tracking-wider">Services</h4>
                <ul class="space-y-2">
                    <li><a href="#" class="hover:text-zinc-900">Virtual Tour 360°</a></li>
                    <li><a href="#" class="hover:text-zinc-900">Lease Management</a></li>
                </ul>
            </div>
            <div>
                <h4 class="font-bold text-zinc-900 mb-3 uppercase text-[10px] tracking-wider">Contact</h4>
                <p>Jakarta, Indonesia</p>
                <p class="mt-1 font-semibold text-zinc-800">+62 812-3456-7890</p>
            </div>
        </div>
    </footer>
</div>