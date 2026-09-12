<?php

use Livewire\Component;

new class extends Component
{
    //
};
?>

<style>
    [x-cloak] { display: none !important; }
    .hotspots-hidden .pnlm-hotspot { display: none !important; }
</style>

<div class="p-6 space-y-6">
    <!-- Header & Breadcrumb -->
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
        <div>
            <a href="{{ route('rooms.index', ['current_team' => auth()->user()->currentTeam->slug]) }}" class="text-xs text-indigo-600 dark:text-indigo-400 font-bold hover:underline">
                ← Kembali ke Daftar Kamar
            </a>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white mt-1">
                Virtual Tour Kamar {{ $room->room_number }}
            </h1>
            <p class="text-xs text-gray-500 dark:text-gray-400">
                {{ $room->property->name ?? 'Properti' }} • {{ $room->type }} • Rp {{ number_format($room->price_per_month, 0, ',', '.') }}/bln
            </p>
        </div>
        <div class="">
            <button wire:click="openSceneModal" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-semibold rounded-lg shadow">
                + Tambah Ruangan (Scene 360°)
            </button>
            @if($activeScene)
            <a href="{{ route('rooms.navigation.edit', ['current_team' => auth()->user()->currentTeam->slug, 'roomNumber' => $room->room_number, 'scene' => $activeScene->id]) }}" class="px-4 py-2 bg-zinc-800 hover:bg-zinc-900 text-white text-xs font-semibold rounded-lg shadow">+ Add Navigation</a>
            @endif
        </div>
    </div>
    <!-- Main Builder Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
        <!-- Sidebar Daftar Scenes -->
        <div class="bg-white dark:bg-zinc-800 p-4 rounded-xl border border-gray-200 dark:border-zinc-700 shadow-sm space-y-3">
            <h3 class="font-bold text-xs uppercase tracking-wider text-gray-500 dark:text-gray-400">Daftar Ruangan (Scenes)</h3>
            
            <div class="space-y-2">
                @forelse($room->scenes as $scene)
                    <div class="w-full p-3 rounded-lg border text-xs flex items-center gap-3
                        {{ $activeSceneId === $scene->id ? 'bg-indigo-50 border-indigo-500 text-indigo-700 dark:bg-zinc-700 dark:text-white font-bold' : 'border-gray-200 dark:border-zinc-700 text-gray-700 dark:text-gray-300 hover:bg-gray-50' }}">
                        <a href="{{ route('rooms.show', ['current_team' => auth()->user()->currentTeam->slug, 'roomNumber' => $room->room_number, 'scene' => $scene->id]) }}" class="flex-1">
                            {{ $scene->title }}
                        </a>
                        @if($scene->is_default)
                            <span class="text-[9px] bg-indigo-200 text-indigo-800 px-1.5 py-0.5 rounded font-bold">Default</span>
                        @endif
                        <a href="{{ route('rooms.navigation.edit', ['current_team' => auth()->user()->currentTeam->slug, 'roomNumber' => $room->room_number, 'scene' => $scene->id]) }}" class="text-[10px] text-indigo-600 hover:underline">Edit navigasi</a>
                        <button type="button" wire:click="deleteScene('{{ $scene->id }}')" wire:confirm="Hapus scene {{ $scene->title }}? Hotspot yang terkait juga akan terhapus." wire:loading.attr="disabled" wire:target="deleteScene('{{ $scene->id }}')" class="text-[10px] text-rose-600 hover:underline disabled:opacity-50" @disabled($room->scenes->count() <= 1)><span wire:loading.remove wire:target="deleteScene('{{ $scene->id }}')">Hapus</span><span wire:loading wire:target="deleteScene('{{ $scene->id }}')">Menghapus...</span></button>
                    </div>
                @empty
                    <p class="text-xs text-gray-400 italic">Belum ada foto 360°. Silakan tambah foto ruangan pertama.</p>
                @endforelse
            </div>
        </div>

        <!-- 360 Viewer Canvas Area -->
        <div x-data="roomTourControls()" x-init="initialize($refs.viewer)" @room-tour:info.window="openInfo($event.detail)" :class="{ 'hotspots-hidden': hotspotsHidden }" class="lg:col-span-3 bg-zinc-900 rounded-xl overflow-hidden shadow-lg border border-zinc-700 relative h-125">
            @if($activeScene)
                <div wire:ignore x-ref="viewer" id="room-panorama-viewer" class="w-full h-full"></div>

                <div class="absolute bottom-6 left-1/2 -translate-x-1/2 z-30 flex items-center gap-1 rounded-2xl border border-white/10 bg-zinc-900/80 px-2 py-1.5 text-white shadow-2xl backdrop-blur-md">
                    <button type="button" @click="toggleAutoRotate" :aria-pressed="autoRotating" :class="autoRotating ? 'bg-indigo-600 text-white' : 'text-zinc-300 hover:bg-white/10'" class="rounded-xl px-3 py-2 text-xs font-semibold transition" title="Putar otomatis">
                        <span x-text="autoRotating ? 'Jeda' : 'Putar'"></span>
                    </button>
                    <button type="button" @click="resetView" class="rounded-xl px-3 py-2 text-xs font-semibold text-zinc-300 transition hover:bg-white/10" title="Reset kamera">Reset</button>
                    <button type="button" @click="zoomOut" class="rounded-xl px-2.5 py-2 text-sm font-bold text-zinc-300 transition hover:bg-white/10" title="Perkecil">−</button>
                    <button type="button" @click="zoomIn" class="rounded-xl px-2.5 py-2 text-sm font-bold text-zinc-300 transition hover:bg-white/10" title="Perbesar">+</button>
                    <button type="button" @click="toggleFullscreen" class="rounded-xl px-3 py-2 text-xs font-semibold text-zinc-300 transition hover:bg-white/10" title="Layar penuh">Layar penuh</button>
                    <button type="button" @click="toggleHotspots" :aria-pressed="!hotspotsHidden" :class="hotspotsHidden ? 'bg-rose-600 text-white' : 'text-zinc-300 hover:bg-white/10'" class="rounded-xl px-3 py-2 text-xs font-semibold transition" title="Tampilkan/sembunyikan label hotspot">
                        <span x-text="hotspotsHidden ? 'Hotspot off' : 'Hotspot on'"></span>
                    </button>
                </div>

                <div x-cloak x-show="isInfoModalOpen" x-transition.opacity class="absolute inset-0 z-40 flex items-center justify-center bg-black/60 p-6 backdrop-blur-sm" @keydown.escape.window="closeInfo">
                    <div x-show="isInfoModalOpen" x-transition class="w-full max-w-md rounded-2xl border border-white/10 bg-zinc-900/95 p-6 text-white shadow-2xl" role="dialog" aria-modal="true" aria-labelledby="room-tour-info-title">
                        <div class="flex items-start justify-between gap-4">
                            <h2 id="room-tour-info-title" class="text-lg font-bold" x-text="modalTitle"></h2>
                            <button type="button" @click="closeInfo" class="rounded-lg px-2 py-1 text-zinc-400 transition hover:bg-white/10 hover:text-white" aria-label="Tutup">&times;</button>
                        </div>
                        <p class="mt-4 whitespace-pre-line text-sm leading-6 text-zinc-300" x-text="modalDesc"></p>
                    </div>
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
                    <button wire:click="storeScene" wire:loading.attr="disabled" wire:target="storeScene" class="px-4 py-2 bg-indigo-600 text-white rounded font-semibold disabled:opacity-50"><span wire:loading.remove wire:target="storeScene">Unggah ke Supabase</span><span wire:loading wire:target="storeScene">Mengunggah...</span></button>
                </div>
            </div>
        </div>
    @endif

    @if($activeScene)
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/pannellum@2.5.6/build/pannellum.css">
        <script src="https://cdn.jsdelivr.net/npm/pannellum@2.5.6/build/pannellum.js"></script>
        <script>
            (() => {
                const tourData = @js($tourData);
                const initialView = { pitch: 0, yaw: 0, hfov: 100 };

                window.roomTourCleanup = window.roomTourCleanup || (() => {});
                window.roomTourControls = () => ({
                    viewer: null,
                    autoRotating: false,
                    hotspotsHidden: false,
                    isInfoModalOpen: false,
                    modalTitle: '',
                    modalDesc: '',
                    initialize(element) {
                        window.roomTourCleanup();
                        const scenes = Object.fromEntries(Object.entries(tourData).map(([sceneId, scene]) => [sceneId, {
                            type: 'equirectangular',
                            panorama: scene.imageUrl,
                            autoLoad: true,
                            hotSpots: (scene.hotspots || []).map((hotspot) => {
                                const pannellumHotspot = {
                                    id: hotspot.id,
                                    pitch: hotspot.pitch,
                                    yaw: hotspot.yaw,
                                    text: hotspot.label || hotspot.title,
                                    cssClass: hotspot.targetSceneId ? undefined : 'room-info-hotspot'
                                };

                                if (hotspot.targetSceneId) {
                                    pannellumHotspot.type = 'scene';
                                    pannellumHotspot.sceneId = hotspot.targetSceneId;
                                } else {
                                    pannellumHotspot.type = 'info';
                                    pannellumHotspot.clickHandlerFunc = () => window.dispatchEvent(new CustomEvent('room-tour:info', {
                                        detail: {
                                            title: hotspot.title || hotspot.label || 'Informasi',
                                            description: hotspot.description || hotspot.label || 'Tidak ada deskripsi.'
                                        }
                                    }));
                                }

                                return pannellumHotspot;
                            })
                        }]));

                        this.viewer = pannellum.viewer(element, {
                            default: {
                                firstScene: @js((string) $activeSceneId),
                                pitch: initialView.pitch,
                                yaw: initialView.yaw,
                                hfov: initialView.hfov,
                                sceneFadeDuration: 500
                            },
                            scenes,
                            showControls: false
                        });
                        window.roomTourViewer = this.viewer;
                        window.roomTourCleanup = () => {
                            if (this.viewer) {
                                this.viewer.destroy();
                                this.viewer = null;
                            }
                            window.roomTourViewer = null;
                        };
                    },
                    toggleAutoRotate() {
                        if (!this.viewer) return;
                        this.autoRotating = !this.autoRotating;
                        this.autoRotating ? this.viewer.startAutoRotate(2) : this.viewer.stopAutoRotate();
                    },
                    resetView() {
                        if (!this.viewer) return;
                        this.viewer.stopAutoRotate();
                        this.autoRotating = false;
                        this.viewer.setPitch(initialView.pitch);
                        this.viewer.setYaw(initialView.yaw);
                        this.viewer.setHfov(initialView.hfov);
                    },
                    zoomIn() {
                        if (this.viewer) this.viewer.setHfov(Math.max(30, this.viewer.getHfov() - 10));
                    },
                    zoomOut() {
                        if (this.viewer) this.viewer.setHfov(Math.min(120, this.viewer.getHfov() + 10));
                    },
                    toggleFullscreen() {
                        if (this.viewer?.toggleFullscreen) {
                            this.viewer.toggleFullscreen();
                        } else {
                            const container = this.$root;
                            document.fullscreenElement ? document.exitFullscreen() : container.requestFullscreen?.();
                        }
                    },
                    toggleHotspots() {
                        this.hotspotsHidden = !this.hotspotsHidden;
                    },
                    openInfo(detail) {
                        this.modalTitle = detail.title;
                        this.modalDesc = detail.description;
                        this.isInfoModalOpen = true;
                    },
                    closeInfo() {
                        this.isInfoModalOpen = false;
                    }
                });

                if (!window.roomTourNavigationCleanupRegistered) {
                    document.addEventListener('livewire:navigating', () => window.roomTourCleanup());
                    window.roomTourNavigationCleanupRegistered = true;
                }
            })();
        </script>
    @endif
</div>