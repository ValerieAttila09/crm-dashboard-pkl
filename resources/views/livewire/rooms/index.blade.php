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

    <!-- Filter Bar & View Toggle -->
    <div class="flex flex-col md:flex-row gap-3 mb-6 items-stretch md:items-center">
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

        <!-- Tombol Toggle Layout (Grid vs List View) -->
        <div class="flex items-center border border-gray-200 dark:border-zinc-700 rounded-lg overflow-hidden p-0.5 bg-gray-50 dark:bg-zinc-800">
            <button wire:click="setViewMode('grid')" type="button" class="p-1.5 rounded-md text-xs font-medium transition flex items-center gap-1 {{ $viewMode === 'grid' ? 'bg-white dark:bg-zinc-700 text-indigo-600 dark:text-indigo-400 shadow-sm' : 'text-gray-500 hover:text-gray-700 dark:text-gray-400' }}" title="Tampilan Grid">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"></path></svg>
                <span class="hidden sm:inline">Grid</span>
            </button>
            <button wire:click="setViewMode('list')" type="button" class="p-1.5 rounded-md text-xs font-medium transition flex items-center gap-1 {{ $viewMode === 'list' ? 'bg-white dark:bg-zinc-700 text-indigo-600 dark:text-indigo-400 shadow-sm' : 'text-gray-500 hover:text-gray-700 dark:text-gray-400' }}" title="Tampilan Tabel/List">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"></path></svg>
                <span class="hidden sm:inline">List</span>
            </button>
        </div>
    </div>

    <!-- TAMPILAN GRID VIEW -->
    @if($viewMode === 'grid')
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @forelse($rooms as $room)
                <div class="bg-white dark:bg-zinc-800 rounded-xl border border-gray-200 dark:border-zinc-700 overflow-hidden shadow-sm flex flex-col justify-between">
                    <div>
                        <!-- Container 360 View Preview / Placeholder -->
                        <div class="relative h-48 bg-zinc-900 flex items-center justify-center">
                            @if($room->panorama_360_url)
                                <div id="panorama-grid-{{ $room->id }}" class="w-full h-full"></div>
                                <script>
                                    document.addEventListener("DOMContentLoaded", function() {
                                        pannellum.viewer('panorama-grid-{{ $room->id }}', {
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
                        <a href="{{ route('rooms.show', ['current_team' => auth()->user()->currentTeam->slug, 'id' => $room->id]) }}" 
                            class="px-3 py-1.5 bg-indigo-600 hover:bg-indigo-700 text-xs font-semibold rounded text-white shadow" 
                            >
                            🌐 Virtual Tour
                        </a>
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

    <!-- TAMPILAN LIST / TABEL VIEW -->
    @else
        <div class="bg-white dark:bg-zinc-800 rounded-xl border border-gray-200 dark:border-zinc-700 overflow-hidden shadow-sm">
            <table class="w-full text-left text-xs text-gray-600 dark:text-gray-300">
                <thead class="bg-gray-50 dark:bg-zinc-900 border-b border-gray-200 dark:border-zinc-700 uppercase font-bold text-[10px]">
                    <tr>
                        <th class="p-3">Nomor Kamar</th>
                        <th class="p-3">Properti / Gedung</th>
                        <th class="p-3">Tipe</th>
                        <th class="p-3">Harga / Bulan</th>
                        <th class="p-3">360° Preview</th>
                        <th class="p-3">Status</th>
                        <th class="p-3 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-zinc-700/50">
                    @forelse($rooms as $room)
                        <tr class="hover:bg-gray-50 dark:hover:bg-zinc-700/30">
                            <td class="p-3 font-bold text-gray-900 dark:text-white">
                                Kamar {{ $room->room_number }}
                            </td>
                            <td class="p-3">
                                {{ $room->property->name ?? '-' }}
                            </td>
                            <td class="p-3">
                                {{ $room->type }}
                            </td>
                            <td class="p-3 font-bold text-indigo-600 dark:text-indigo-400">
                                Rp {{ number_format($room->price_per_month, 0, ',', '.') }}
                            </td>
                            <td class="p-3">
                                @if($room->panorama_360_url)
                                    <span class="inline-flex items-center gap-1 text-[10px] bg-indigo-50 text-indigo-700 dark:bg-indigo-950 dark:text-indigo-300 px-2 py-0.5 rounded-full font-bold">
                                        🌐 Available
                                    </span>
                                @else
                                    <span class="text-gray-400 text-[10px]">-</span>
                                @endif
                            </td>
                            <td class="p-3">
                                <span class="text-[10px] font-bold uppercase px-2 py-0.5 rounded-full 
                                    {{ $room->status === 'available' ? 'bg-emerald-100 text-emerald-800' : ($room->status === 'occupied' ? 'bg-indigo-100 text-indigo-800' : 'bg-amber-100 text-amber-800') }}">
                                    {{ $room->status }}
                                </span>
                            </td>
                            <td class="p-3 flex items-center gap-2">
                                <a href="{{ route('rooms.show', ['current_team' => auth()->user()->currentTeam->slug, 'id' => $room->id]) }}" 
                                    class="px-3 py-1.5 bg-indigo-600 hover:bg-indigo-700 text-xs font-semibold rounded text-white shadow" 
                                    >
                                    🌐 Virtual Tour
                                </a>
                                <button wire:click="edit('{{ $room->id }}')" class="px-2.5 py-1 bg-gray-200 dark:bg-zinc-700 hover:bg-gray-300 text-xs font-semibold rounded text-gray-800 dark:text-gray-200">
                                    Edit
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="p-4 text-center text-gray-400">Belum ada unit kamar.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    @endif

    <!-- Modal Form Tambah/Edit -->
    @if($isModalOpen)
        <div class="fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4">
            <div class="bg-white dark:bg-zinc-800 rounded-xl p-6 w-full max-w-lg shadow-xl border border-zinc-700">
                <h2 class="text-lg font-bold text-gray-900 dark:text-white mb-4">{{ $roomId ? 'Edit Kamar' : 'Tambah Kamar Baru' }}</h2>

                <div class="space-y-3 text-xs">
                    <!-- Dropdown & Form Inline Tambah Properti -->
                    <div>
                        <div class="flex justify-between items-center mb-1">
                            <label class="block font-semibold dark:text-gray-300">Pilih Properti</label>
                            <button type="button" wire:click="toggleCreateProperty" class="text-[11px] font-bold text-indigo-600 dark:text-indigo-400 hover:underline">
                                {{ $isCreatingProperty ? '← Pilih dari Daftar' : '+ Properti Baru' }}
                            </button>
                        </div>

                        @if($isCreatingProperty)
                            <!-- Form Input Properti Cepat -->
                            <div class="p-3 bg-indigo-50/50 dark:bg-zinc-900/60 rounded-lg border border-indigo-100 dark:border-zinc-700 space-y-2 mb-2">
                                <div>
                                    <input type="text" wire:model="new_property_name" placeholder="Nama Properti / Gedung (misal: Kos Melati)" class="w-full p-2 border rounded text-xs dark:bg-zinc-900 dark:border-zinc-700 dark:text-white">
                                    @error('new_property_name') <span class="text-red-500 text-[10px]">{{ $message }}</span> @enderror
                                </div>
                                <div>
                                    <input type="text" wire:model="new_property_address" placeholder="Alamat Singkat (Opsional)" class="w-full p-2 border rounded text-xs dark:bg-zinc-900 dark:border-zinc-700 dark:text-white">
                                </div>
                                <button type="button" wire:click="storeProperty" class="w-full py-1.5 bg-indigo-600 hover:bg-indigo-700 text-white font-bold rounded text-[11px]">
                                    Simpan & Gunakan Properti
                                </button>
                            </div>
                        @else
                            <!-- Dropdown Pilih Properti -->
                            <select wire:model="property_id" class="w-full p-2 border rounded dark:bg-zinc-900 dark:border-zinc-700 dark:text-white">
                                <option value="">-- Pilih Properti --</option>
                                @foreach($properties as $p)
                                    <option value="{{ $p->id }}">{{ $p->name }}</option>
                                @endforeach
                            </select>
                            @error('property_id') <span class="text-red-500 text-[10px]">{{ $message }}</span> @enderror
                        @endif
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
                        <label class="block font-semibold mb-1 dark:text-gray-300">Upload Gambar 360° Panorama</label>
                        
                        <input type="file" wire:model="panorama_image" accept="image/*" class="w-full text-xs p-1.5 border rounded dark:bg-zinc-900 dark:border-zinc-700 dark:text-white file:mr-2 file:py-1 file:px-2 file:rounded file:border-0 file:text-xs file:font-semibold file:bg-indigo-50 file:text-indigo-700 dark:file:bg-zinc-800 dark:file:text-zinc-300">
                        
                        <!-- Indicator Loading saat Upload File -->
                        <div wire:loading wire:target="panorama_image" class="text-[10px] text-indigo-500 font-semibold mt-1">
                            Mengunggah berkas gambar 360°...
                        </div>

                        @error('panorama_image') 
                            <span class="text-red-500 text-[10px] block mt-0.5">{{ $message }}</span> 
                        @enderror

                        <!-- Preview jika sudah ada file lama atau baru di-upload -->
                        @if ($panorama_image)
                            <div class="mt-2 text-[10px] text-emerald-600 font-bold">✓ File baru siap disimpan</div>
                        @elseif ($old_panorama_url)
                            <div class="mt-2 text-[10px] text-gray-400">Gambar 360 saat ini: <a href="{{ $old_panorama_url }}" target="_blank" class="text-indigo-500 underline">Lihat Berkas</a></div>
                        @endif
                        
                        <span class="text-[10px] text-gray-400 block mt-1">Format gambar equirectangular (JPG/PNG). Maksimal ukuran file 10MB.</span>
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