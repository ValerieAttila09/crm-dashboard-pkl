<?php

use Livewire\Component;

new class extends Component
{
    //
};
?>

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

        <details open class="rounded-lg border border-amber-300 bg-amber-50 p-3 text-[11px] text-amber-950 dark:border-amber-700 dark:bg-amber-950/30 dark:text-amber-100">
            <summary class="cursor-pointer font-bold">Debug Data Virtual Tour</summary>
            <div class="mt-3 grid gap-2 sm:grid-cols-2">
                <div><b>Room ID:</b> <span class="break-all">{{ $room->id }}</span></div>
                <div><b>Room Number:</b> {{ $room->room_number }}</div>
                <div><b>Active Scene ID:</b> <span class="break-all">{{ $activeScene?->id ?? 'null' }}</span></div>
                <div><b>Active Scene:</b> {{ $activeScene?->title ?? 'null' }}</div>
                <div class="sm:col-span-2"><b>Active Image URL:</b> <span class="break-all">{{ $activeScene?->image_url ?? 'null' }}</span></div>
            </div>
            <div class="mt-3 overflow-x-auto rounded border border-amber-200 bg-white/70 dark:border-amber-800 dark:bg-zinc-900/50">
                <table class="w-full text-left">
                    <thead class="border-b border-amber-200 dark:border-amber-800">
                        <tr><th class="p-2">Scene ID</th><th class="p-2">Title</th><th class="p-2">Image URL</th></tr>
                    </thead>
                    <tbody>
                        @forelse($room->scenes as $debugScene)
                            <tr class="border-b border-amber-100 last:border-0 dark:border-amber-900/50">
                                <td class="p-2 break-all">{{ $debugScene->id }}</td>
                                <td class="p-2">{{ $debugScene->title }}</td>
                                <td class="p-2 break-all">{{ $debugScene->image_url }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="3" class="p-2">Tidak ada scene.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </details>

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
                    <a href="{{ route('rooms.show', ['current_team' => auth()->user()->currentTeam->slug, 'roomNumber' => $room->room_number, 'scene' => $scene->id]) }}" 
                        class="w-full text-left p-3 rounded-lg border text-xs flex justify-between items-center transition
                        {{ $activeSceneId === $scene->id ? 'bg-indigo-50 border-indigo-500 text-indigo-700 dark:bg-zinc-700 dark:text-white font-bold' : 'border-gray-200 dark:border-zinc-700 text-gray-700 dark:text-gray-300 hover:bg-gray-50' }}">
                        <span>{{ $scene->title }}</span>
                        @if($scene->is_default)
                            <span class="text-[9px] bg-indigo-200 text-indigo-800 px-1.5 py-0.5 rounded font-bold">Default</span>
                        @endif
                    </a>
                @empty
                    <p class="text-xs text-gray-400 italic">Belum ada foto 360°. Silakan tambah foto ruangan pertama.</p>
                @endforelse
            </div>
        </div>

        <!-- 360 Viewer Canvas Area -->
        <div class="lg:col-span-3 bg-zinc-900 rounded-xl overflow-hidden shadow-lg border border-zinc-700 relative h-125">
            @if($activeScene)
                <img
                    id="panorama-source"
                    wire:key="panorama-source-{{ $activeScene->id }}"
                    src="{{ $activeScene->image_url }}"
                    alt=""
                    class="hidden"
                >
                <div wire:ignore id="native-panorama" class="relative w-full h-full bg-zinc-950">
                    <canvas id="panorama-canvas" class="block w-full h-full"></canvas>
                    <div id="panorama-status" class="absolute inset-0 flex items-center justify-center text-xs text-white/70">Memuat panorama...</div>
                    <div class="absolute top-3 left-3 flex gap-1">
                        <button type="button" data-panorama-action="zoom-out" class="w-8 h-8 rounded bg-black/60 text-white text-lg" aria-label="Perkecil">-</button>
                        <button type="button" data-panorama-action="zoom-in" class="w-8 h-8 rounded bg-black/60 text-white text-lg" aria-label="Perbesar">+</button>
                        <button type="button" data-panorama-action="fullscreen" class="w-8 h-8 rounded bg-black/60 text-white text-sm" aria-label="Layar penuh">□</button>
                    </div>
                    <div class="absolute bottom-3 left-3 bg-black/60 text-white text-[10px] px-3 py-1.5 rounded-lg">Seret untuk melihat sekeliling</div>
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

    @if($activeScene)
    <script>
        (() => {
            if (window.nativeRoomPanorama) return;

            const canvas = document.getElementById('panorama-canvas');
            const status = document.getElementById('panorama-status');
            if (!canvas) return;

            const context = canvas.getContext('2d');
            if (!context) {
                status.textContent = 'Browser tidak mendukung canvas.';
                return;
            }

            let panorama = null;
            let yaw = 0;
            let pitch = 0;
            let fov = 75;
            let dragging = false;
            let lastX = 0;
            let lastY = 0;
            let loadNumber = 0;
            let currentUrl = '';

            function resize() {
                const ratio = window.devicePixelRatio || 1;
                canvas.width = canvas.clientWidth * ratio;
                canvas.height = canvas.clientHeight * ratio;
                draw();
            }

            function draw() {
                if (!panorama) return;

                const width = canvas.width;
                const height = canvas.height;
                const sourceWidth = Math.max(1, panorama.width * (fov / 360));
                const sourceHeight = panorama.height;
                const sourceX = ((panorama.width * (0.5 + yaw / (Math.PI * 2))) - sourceWidth / 2 + panorama.width * 2) % panorama.width;
                const offsetY = Math.max(0, Math.min(panorama.height - sourceHeight, pitch * panorama.height / Math.PI));
                const scale = Math.max(width / sourceWidth, height / sourceHeight);
                const drawWidth = sourceWidth * scale;
                const drawHeight = sourceHeight * scale;
                const drawY = (height - drawHeight) / 2;

                context.fillStyle = '#09090b';
                context.fillRect(0, 0, width, height);
                context.drawImage(panorama, sourceX, offsetY, Math.min(sourceWidth, panorama.width - sourceX), sourceHeight, 0, drawY, Math.min(sourceWidth, panorama.width - sourceX) * scale, drawHeight);
                if (sourceX + sourceWidth > panorama.width) {
                    context.drawImage(panorama, 0, offsetY, sourceWidth - (panorama.width - sourceX), sourceHeight, (panorama.width - sourceX) * scale, drawY, (sourceWidth - (panorama.width - sourceX)) * scale, drawHeight);
                }
            }

            function loadPanorama(url) {
                if (!url) return;
                if (url === currentUrl && panorama) return;

                const currentLoad = ++loadNumber;
                currentUrl = url;
                status.textContent = 'Memuat panorama...';
                status.classList.remove('hidden');
                const image = new Image();
                image.onload = () => {
                    if (currentLoad !== loadNumber) return;
                    const finishLoad = () => {
                        if (currentLoad !== loadNumber) return;
                        panorama = image;
                        status.classList.add('hidden');
                        resize();
                    };
                    if (typeof image.decode === 'function') {
                        image.decode().then(finishLoad).catch(finishLoad);
                    } else {
                        finishLoad();
                    }
                };
                image.onerror = () => {
                    if (currentLoad !== loadNumber) return;
                    status.textContent = 'Panorama gagal dimuat. Periksa URL atau izin CORS Storage.';
                };
                image.src = url;
            }

            function syncSourceImage() {
                const source = document.getElementById('panorama-source');
                if (source?.src) loadPanorama(source.src);
            }

            canvas.addEventListener('pointerdown', (event) => {
                dragging = true;
                lastX = event.clientX;
                lastY = event.clientY;
                canvas.setPointerCapture(event.pointerId);
            });
            canvas.addEventListener('pointermove', (event) => {
                if (!dragging) return;
                yaw -= (event.clientX - lastX) * 0.006;
                pitch = Math.max(-1.45, Math.min(1.45, pitch + (event.clientY - lastY) * 0.006));
                lastX = event.clientX;
                lastY = event.clientY;
                draw();
            });
            canvas.addEventListener('pointerup', () => { dragging = false; });
            canvas.addEventListener('pointercancel', () => { dragging = false; });
            canvas.addEventListener('wheel', (event) => {
                event.preventDefault();
                fov = Math.max(35, Math.min(100, fov + event.deltaY * 0.04));
                draw();
            }, { passive: false });
            document.querySelectorAll('[data-panorama-action]').forEach((button) => {
                button.addEventListener('click', () => {
                    const action = button.dataset.panoramaAction;
                    if (action === 'zoom-in') fov = Math.max(35, fov - 8);
                    if (action === 'zoom-out') fov = Math.min(100, fov + 8);
                    if (action === 'fullscreen') document.getElementById('native-panorama').requestFullscreen?.();
                    draw();
                });
            });
            new ResizeObserver(resize).observe(canvas);

            window.nativeRoomPanorama = { loadPanorama };
            syncSourceImage();
            Livewire.on('load-scene', (event) => {
                console.group('[Room Tour Debug] Scene dipilih');
                console.log('Room ID:', @js($room->id));
                console.log('Scene ID:', event.sceneId);
                console.log('Image URL:', event.imageUrl);
                console.groupEnd();
                if (event.imageUrl) loadPanorama(event.imageUrl);
            });
            const sourceObserver = new MutationObserver(syncSourceImage);
            sourceObserver.observe(document.body, { childList: true, subtree: true, attributes: true, attributeFilter: ['src'] });
            Livewire.hook('morph.updated', syncSourceImage);
        })();
    </script>
    @endif
</div>