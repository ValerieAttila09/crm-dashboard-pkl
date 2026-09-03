<?php

use Livewire\Component;

new class extends Component
{
    //
};
?>

<div class="p-6 space-y-6">
    <!-- Pannellum CDN -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/pannellum@2.5.6/build/pannellum.css"/>
    <script src="https://cdn.jsdelivr.net/npm/pannellum@2.5.6/build/pannellum.js"></script>

    <!-- Header & Breadcrumb -->
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
        <div>
            <a href="{{ route('rooms.index', ['current_team' => auth()->user()->currentTeam->slug]) }}" class="text-xs text-indigo-600 dark:text-indigo-400 font-bold hover:underline" wire:navigate>
                ← Kembali ke Daftar Kamar
            </a>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white mt-1">
                Virtual Tour Kamar {{ $room->room_number }}
            </h1>
            <p class="text-xs text-gray-500 dark:text-gray-400">
                {{ $room->property->name ?? 'Properti' }} • {{ $room->type }} • Rp {{ number_format($room->price_per_month, 0, ',', '.') }}/bln
            </p>
        </div>

        <button wire:click="openSceneModal" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-semibold rounded-lg shadow">
            + Tambah Ruangan (Scene 360°)
        </button>
    </div>

    <!-- Main Builder Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
        <!-- Sidebar Daftar Scenes -->
        <div class="bg-white dark:bg-zinc-800 p-4 rounded-xl border border-gray-200 dark:border-zinc-700 shadow-sm space-y-3">
            <h3 class="font-bold text-xs uppercase tracking-wider text-gray-500 dark:text-gray-400">Daftar Ruangan (Scenes)</h3>
            
            <div class="space-y-2">
                @forelse($room->scenes as $scene)
                    <button wire:click="selectScene('{{ $scene->id }}')" 
                        class="w-full text-left p-3 rounded-lg border text-xs flex justify-between items-center transition
                        {{ $activeSceneId === $scene->id ? 'bg-indigo-50 border-indigo-500 text-indigo-700 dark:bg-zinc-700 dark:text-white font-bold' : 'border-gray-200 dark:border-zinc-700 text-gray-700 dark:text-gray-300 hover:bg-gray-50' }}">
                        <span>{{ $scene->title }}</span>
                        @if($scene->is_default)
                            <span class="text-[9px] bg-indigo-200 text-indigo-800 px-1.5 py-0.5 rounded font-bold">Default</span>
                        @endif
                    </button>
                @empty
                    <p class="text-xs text-gray-400 italic">Belum ada foto 360°. Silakan tambah foto ruangan pertama.</p>
                @endforelse
            </div>
        </div>

        <!-- 360 Viewer Canvas Area -->
        <div class="lg:col-span-3 bg-zinc-900 rounded-xl overflow-hidden shadow-lg border border-zinc-700 relative h-[500px]">
            @if($activeScene)
                <div wire:ignore id="panorama-viewer" class="w-full h-full"></div>

                <div class="absolute bottom-3 left-3 bg-black/70 text-white text-[10px] px-3 py-1.5 rounded-lg backdrop-blur">
                    💡 <b>Petunjuk Admin:</b> Klik di mana saja pada tampilan 360° untuk memasang <b>Hotspot Navigasi (Floating Button)</b>.
                </div>
            @else
                <div class="flex items-center justify-center h-full text-zinc-500 text-sm">
                    Pilih atau tambahkan scene 360° terlebih dahulu.
                </div>
            @endif
        </div>
    </div>

    <!-- Modal Form Tambah Scene -->
    @if($isSceneModalOpen)
        <div class="fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4">
            <div class="bg-white dark:bg-zinc-800 rounded-xl p-6 w-full max-w-md shadow-xl border border-zinc-700 space-y-4 text-xs">
                <h2 class="text-base font-bold text-gray-900 dark:text-white">Tambah Ruangan 360° Baru</h2>

                <div>
                    <label class="block font-semibold mb-1 dark:text-gray-300">Nama Ruangan</label>
                    <input type="text" wire:model="new_scene_title" placeholder="misal: Ruang Tamu / Kamar Utama / Balkon" class="w-full p-2 border rounded dark:bg-zinc-900 dark:border-zinc-700 dark:text-white">
                </div>

                <div>
                    <label class="block font-semibold mb-1 dark:text-gray-300">Upload File Foto 360°</label>
                    <input type="file" wire:model="new_scene_image" accept="image/*" class="w-full p-1.5 border rounded dark:bg-zinc-900 dark:border-zinc-700 dark:text-white">
                </div>

                <div class="flex items-center gap-2">
                    <input type="checkbox" wire:model="is_default" id="is_default">
                    <label for="is_default" class="dark:text-gray-300">Jadikan Ruangan Utama (Default View)</label>
                </div>

                <div class="flex justify-end gap-2 pt-2">
                    <button wire:click="$set('isSceneModalOpen', false)" class="px-4 py-2 bg-gray-200 rounded font-semibold">Batal</button>
                    <button wire:click="storeScene" class="px-4 py-2 bg-indigo-600 text-white rounded font-semibold">Unggah ke Supabase</button>
                </div>
            </div>
        </div>
    @endif

    <!-- Modal Form Tambah Hotspot (Klik Result) -->
    @if($isHotspotModalOpen)
        <div class="fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4">
            <div class="bg-white dark:bg-zinc-800 rounded-xl p-6 w-full max-w-md shadow-xl border border-zinc-700 space-y-4 text-xs">
                <h2 class="text-base font-bold text-gray-900 dark:text-white">Pasang Hotspot Navigasi</h2>

                <div class="p-2.5 bg-indigo-50 dark:bg-zinc-900 rounded-lg text-[11px] space-y-1">
                    <p class="font-bold text-indigo-700 dark:text-indigo-300">Koordinat Tertangkap:</p>
                    <p class="text-gray-600 dark:text-gray-400">Pitch (Y): <b>{{ $clickedPitch }}</b> | Yaw (X): <b>{{ $clickedYaw }}</b></p>
                </div>

                <div>
                    <label class="block font-semibold mb-1 dark:text-gray-300">Pilih Ruangan Tujuan</label>
                    <select wire:model="target_scene_id" class="w-full p-2 border rounded dark:bg-zinc-900 dark:border-zinc-700 dark:text-white">
                        <option value="">-- Pilih Ruangan Tujuan --</option>
                        @foreach($room->scenes->where('id', '!=', $activeSceneId) as $sc)
                            <option value="{{ $sc->id }}">{{ $sc->title }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block font-semibold mb-1 dark:text-gray-300">Label Tooltip Tombol</label>
                    <input type="text" wire:model="hotspot_title" placeholder="misal: Masuk ke Kamar Mandi" class="w-full p-2 border rounded dark:bg-zinc-900 dark:border-zinc-700 dark:text-white">
                </div>

                <div class="flex justify-end gap-2 pt-2">
                    <button wire:click="$set('isHotspotModalOpen', false)" class="px-4 py-2 bg-gray-200 rounded font-semibold">Batal</button>
                    <button wire:click="storeHotspot" class="px-4 py-2 bg-indigo-600 text-white rounded font-semibold">Simpan Hotspot</button>
                </div>
            </div>
        </div>
    @endif

    <!-- Pannellum JS Script Integrator -->
    @if($activeScene)
    <script>
        let viewerInstance = null;

        function initOrUpdatePannellum(imageUrl, hotspotsData) {
            const container = document.getElementById('panorama-viewer');
            if (!container) return;

            // 1. Jika instance sudah ada, hancurkan secara bersih terlebih dahulu
            if (viewerInstance) {
                try {
                    viewerInstance.destroy();
                } catch (e) {
                    console.log('Clearing old instance');
                }
                viewerInstance = null;
            }

            // 2. Jika gambar valid, buat instance baru
            if (imageUrl) {
                viewerInstance = pannellum.viewer('panorama-viewer', {
                    "type": "equirectangular",
                    "panorama": imageUrl,
                    "autoLoad": true,
                    "hotSpots": hotspotsData || []
                });

                // Tangkap klik mouse untuk penentuan koordinat Hotspot
                viewerInstance.on('mousedown', function(event) {
                    const coords = viewerInstance.mouseEventToCoords(event);
                    if (coords && coords[0] !== undefined) {
                        @this.call('captureHotspotCoords', coords[0], coords[1]);
                    }
                });
            }
        }

        function renderCurrentScene() {
            @if($activeScene && $activeScene->image_url)
                const imageUrl = "{{ $activeScene->image_url }}";
                const hotspotsData = [
                    @foreach($activeScene->hotspots as $hs)
                    {
                        "pitch": {{ $hs->pitch }},
                        "yaw": {{ $hs->yaw }},
                        "type": "scene",
                        "text": "{{ $hs->title }}",
                        "sceneId": "{{ $hs->target_scene_id }}",
                        "clickHandlerFunc": function() {
                            @this.call('selectScene', '{{ $hs->target_scene_id }}');
                        }
                    },
                    @endforeach
                ];

                initOrUpdatePannellum(imageUrl, hotspotsData);
            @endif
        }

        // Dipanggil saat halaman pertama kali dibuka
        document.addEventListener('DOMContentLoaded', () => {
            setTimeout(renderCurrentScene, 100);
        });

        // Dipanggil saat navigasi antar-halaman Livewire (wire:navigate)
        document.addEventListener('livewire:navigated', () => {
            setTimeout(renderCurrentScene, 100);
        });

        // Dipanggil setiap kali event Livewire 'load-scene' dipicu
        Livewire.on('load-scene', () => {
            setTimeout(renderCurrentScene, 150);
        });
    </script>
    @endif
</div>