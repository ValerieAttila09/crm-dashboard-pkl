<div class="min-h-screen bg-zinc-950 text-white">
    <div class="flex flex-col gap-4 border-b border-zinc-800 px-5 py-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <a href="{{ route('rooms.show', ['current_team' => auth()->user()->currentTeam->slug, 'roomNumber' => $room->room_number, 'scene' => $activeScene->id]) }}" class="text-xs text-indigo-300 hover:underline">Kembali ke Virtual Tour</a>
            <h1 class="mt-1 text-xl font-bold">Editor Navigation {{ $room->room_number }}</h1>
            <p class="text-xs text-zinc-400">Scene: {{ $activeScene->title }}</p>
        </div>
        <div class="flex flex-wrap gap-2">
            <button type="button" id="add-hotspot" class="rounded-lg bg-indigo-600 px-4 py-2 text-xs font-semibold hover:bg-indigo-500">+ Add Hotspot</button>
            <button type="button" id="undo-hotspot" class="rounded-lg bg-zinc-800 px-3 py-2 text-xs font-semibold">Undo</button>
            <button type="button" id="redo-hotspot" class="rounded-lg bg-zinc-800 px-3 py-2 text-xs font-semibold">Redo</button>
            <button type="button" id="reset-hotspot" class="rounded-lg bg-rose-900/80 px-3 py-2 text-xs font-semibold">Reset</button>
        </div>
    </div>

    <div class="grid min-h-[calc(100vh-85px)] grid-cols-1 lg:grid-cols-[minmax(0,1fr)_300px]">
        <div class="relative min-h-[520px] bg-black">
            <div id="navigation-panorama" wire:ignore class="absolute inset-0"></div>
            <div class="pointer-events-none absolute bottom-4 left-4 rounded bg-black/70 px-3 py-2 text-xs text-zinc-200">Aktifkan Add Hotspot, lalu klik posisi pada panorama.</div>
        </div>
        <aside class="border-t border-zinc-800 bg-zinc-900 p-5 lg:border-l lg:border-t-0">
            <h2 class="font-semibold">Scene Tujuan</h2>
            <div class="mt-3 space-y-2">
                @foreach($scenes as $targetScene)
                    <a href="{{ route('rooms.navigation.edit', ['current_team' => auth()->user()->currentTeam->slug, 'roomNumber' => $room->room_number, 'scene' => $targetScene->id]) }}" class="block rounded border border-zinc-700 px-3 py-2 text-xs hover:border-indigo-400 {{ $targetScene->id === $activeScene->id ? 'border-indigo-500 bg-indigo-950/50' : '' }}">{{ $targetScene->title }}</a>
                @endforeach
            </div>
            <div class="mt-8 border-t border-zinc-800 pt-5 text-xs text-zinc-400">
                <p>Hotspot tersimpan: {{ $hotspots->count() }}</p>
                <p class="mt-2">Undo/Redo/Reset mengelola marker draft di editor. Data disimpan setelah formulir hotspot dikonfirmasi.</p>
            </div>
        </aside>
    </div>

    @if($isHotspotModalOpen)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/70 p-4">
            <form wire:submit="storeHotspot" class="w-full max-w-lg space-y-4 rounded-xl bg-white p-6 text-zinc-900 shadow-2xl">
                <div class="flex items-start justify-between gap-4">
                    <div><h2 class="text-lg font-bold">Detail Navigation</h2><p class="text-xs text-zinc-500">Pitch: {{ $pitch }} | Yaw: {{ $yaw }}</p></div>
                    <button type="button" wire:click="$set('isHotspotModalOpen', false)" class="text-zinc-500">X</button>
                </div>
                <input wire:model="hotspotTitle" class="w-full rounded border p-2 text-sm" placeholder="Nama Navigation">
                @error('hotspotTitle') <p class="text-xs text-red-600">{{ $message }}</p> @enderror
                <input wire:model="hotspotLabel" class="w-full rounded border p-2 text-sm" placeholder="Label tombol">
                @error('hotspotLabel') <p class="text-xs text-red-600">{{ $message }}</p> @enderror
                <textarea wire:model="hotspotDescription" class="w-full rounded border p-2 text-sm" rows="3" placeholder="Description"></textarea>
                <select wire:model="targetSceneId" class="w-full rounded border p-2 text-sm">
                    <option value="">Pilih tujuan ruangan</option>
                    @foreach($scenes->where('id', '!=', $activeScene->id) as $targetScene)
                        <option value="{{ $targetScene->id }}">{{ $targetScene->title }}</option>
                    @endforeach
                </select>
                @error('targetSceneId') <p class="text-xs text-red-600">{{ $message }}</p> @enderror
                <div class="flex justify-end gap-2"><button type="button" wire:click="$set('isHotspotModalOpen', false)" class="rounded bg-zinc-200 px-4 py-2 text-sm">Batal</button><button class="rounded bg-indigo-600 px-4 py-2 text-sm font-semibold text-white">Simpan</button></div>
            </form>
        </div>
    @endif

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/pannellum@2.5.6/build/pannellum.css">
    <script src="https://cdn.jsdelivr.net/npm/pannellum@2.5.6/build/pannellum.js"></script>
    <script>
        (() => {
            if (window.navigationEditor) return;
            const viewer = pannellum.viewer('navigation-panorama', { type: 'equirectangular', panorama: @js($activeScene->image_url), autoLoad: true, hotSpots: @js($hotspots->map(fn ($hotspot) => ['id' => (string) $hotspot->id, 'pitch' => (float) $hotspot->pitch, 'yaw' => (float) $hotspot->yaw, 'type' => 'info', 'text' => $hotspot->label ?: $hotspot->title])->values()) });
            let placing = false;
            let history = [];
            let historyIndex = -1;
            const snapshot = () => historyIndex < 0 ? [] : history[historyIndex];
            const saveHistory = (items) => { history = history.slice(0, historyIndex + 1); history.push(items); historyIndex++; };
            const renderDraft = (items) => { document.querySelectorAll('.pnlm-hotspot').forEach((el) => el.remove()); items.forEach((item) => viewer.addHotSpot(item)); };
            document.getElementById('add-hotspot').onclick = () => { placing = true; document.getElementById('add-hotspot').textContent = 'Klik Panorama...'; };
            viewer.on('mousedown', (event) => { if (!placing) return; const coords = viewer.mouseEventToCoords(event); if (!coords) return; placing = false; document.getElementById('add-hotspot').textContent = '+ Add Hotspot'; const item = { id: `draft-${Date.now()}`, pitch: coords[0], yaw: coords[1], type: 'info', text: 'Draft hotspot' }; const next = [...snapshot(), item]; saveHistory(next); renderDraft(next); @this.call('openHotspotModal', coords[0], coords[1]); });
            document.getElementById('undo-hotspot').onclick = () => { if (historyIndex > 0) { historyIndex--; renderDraft(snapshot()); } };
            document.getElementById('redo-hotspot').onclick = () => { if (historyIndex < history.length - 1) { historyIndex++; renderDraft(snapshot()); } };
            document.getElementById('reset-hotspot').onclick = () => { saveHistory([]); renderDraft([]); };
            window.navigationEditor = viewer;
        })();
    </script>
</div>
