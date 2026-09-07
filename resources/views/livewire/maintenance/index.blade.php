<div class="p-6 bg-slate-50 dark:bg-zinc-900 min-h-screen">

    <!-- Flash Message -->
    @if (session()->has('message'))
        <div class="mb-4 p-3 bg-emerald-100 border border-emerald-300 text-emerald-800 rounded-lg text-xs font-semibold flex items-center justify-between">
            <span>{{ session('message') }}</span>
        </div>
    @endif

    <!-- 1. STAT CARDS & FINANCIAL NET PROFIT SUMMARY -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
        <!-- Pendapatan Kotor -->
        <div class="p-4 bg-white dark:bg-zinc-800 rounded-2xl border border-gray-200 dark:border-zinc-700 shadow-sm">
            <p class="text-xs font-semibold text-gray-400">Total Pendapatan Sewa (Paid)</p>
            <h3 class="text-xl font-bold text-gray-800 dark:text-gray-100 mt-1">Rp {{ number_format($grossRevenue, 0, ',', '.') }}</h3>
        </div>

        <!-- Total Biaya Maintenance (Expenses) -->
        <div class="p-4 bg-white dark:bg-zinc-800 rounded-2xl border border-gray-200 dark:border-zinc-700 shadow-sm">
            <p class="text-xs font-semibold text-red-500">Total Biaya Perbaikan (Completed)</p>
            <h3 class="text-xl font-bold text-red-600 dark:text-red-400 mt-1">- Rp {{ number_format($totalMaintenanceCost, 0, ',', '.') }}</h3>
        </div>

        <!-- Profit Bersih (Net Profit) -->
        <div class="p-4 bg-indigo-50 dark:bg-indigo-950/40 rounded-2xl border border-indigo-200 dark:border-indigo-800 shadow-sm">
            <p class="text-xs font-semibold text-indigo-600 dark:text-indigo-400">Estimasi Net Profit</p>
            <h3 class="text-xl font-black text-indigo-700 dark:text-indigo-300 mt-1">Rp {{ number_format($netProfit, 0, ',', '.') }}</h3>
        </div>

        <!-- Status Tiket Aktif -->
        <div class="p-4 bg-white dark:bg-zinc-800 rounded-2xl border border-gray-200 dark:border-zinc-700 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold text-gray-400">Tiket Aktif</p>
                <div class="flex items-center gap-2 mt-1">
                    <span class="text-xs font-bold px-2 py-0.5 bg-amber-100 text-amber-800 rounded-md">{{ $pendingCount }} Pending</span>
                    <span class="text-xs font-bold px-2 py-0.5 bg-blue-100 text-blue-800 rounded-md">{{ $inProgressCount }} In Progress</span>
                </div>
            </div>
        </div>
    </div>

    <!-- 2. TABLE HEADER & FILTERS -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Laporan Keluhan & Perbaikan</h1>
            <p class="text-xs text-gray-500">Pantau perbaikan fasilitas dan dampaknya terhadap pendapatan bersih.</p>
        </div>
        <button wire:click="$set('isModalOpen', true)" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg text-xs font-semibold shadow-sm transition">
            + Laporkan Kerusakan
        </button>
    </div>

    <!-- 3. DATA TABLE -->
    <div class="bg-white dark:bg-zinc-800 rounded-2xl border border-gray-200 dark:border-zinc-700 shadow-sm overflow-hidden">
        <table class="w-full text-left border-collapse text-xs">
            <thead class="bg-gray-50 dark:bg-zinc-900/50 border-b border-gray-200 dark:border-zinc-700 text-gray-400 font-bold uppercase">
                <tr>
                    <th class="p-3">Judul & Detail</th>
                    <th class="p-3">Kamar</th>
                    <th class="p-3">Pelapor</th>
                    <th class="p-3">Prioritas</th>
                    <th class="p-3">Biaya Perbaikan</th>
                    <th class="p-3">Status Pekerjaan</th>
                    <th class="p-3">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 dark:divide-zinc-700/60">
                @forelse($requests as $req)
                    <tr class="hover:bg-gray-50/50 dark:hover:bg-zinc-700/20 transition">
                        <td class="p-3">
                            <p class="font-bold text-gray-800 dark:text-gray-200">{{ $req->title }}</p>
                            <p class="text-[11px] text-gray-400 truncate max-w-xs">{{ $req->description }}</p>
                        </td>
                        <td class="p-3 font-semibold text-indigo-600">
                            Kamar {{ $req->room->room_number ?? '-' }}
                        </td>
                        <td class="p-3 text-gray-600 dark:text-gray-300">
                            {{ $req->customer->name ?? 'Pengelola' }}
                        </td>
                        <td class="p-3">
                            <span class="px-2 py-0.5 rounded text-[10px] font-black uppercase
                                {{ $req->priority === 'urgent' ? 'bg-red-100 text-red-800' : '' }}
                                {{ $req->priority === 'high' ? 'bg-amber-100 text-amber-800' : '' }}
                                {{ $req->priority === 'medium' ? 'bg-blue-100 text-blue-800' : '' }}
                                {{ $req->priority === 'low' ? 'bg-gray-100 text-gray-800' : '' }}">
                                {{ $req->priority }}
                            </span>
                        </td>
                        <td class="p-3 font-bold text-gray-800 dark:text-gray-100">
                            Rp {{ number_format($req->cost, 0, ',', '.') }}
                        </td>
                        <!-- Quick Status Toggle -->
                        <td class="p-3">
                            <select wire:change="updateQuickStatus('{{ $req->id }}', $event.target.value)" 
                                    class="text-[11px] font-extrabold rounded-lg border-0 py-1 px-2.5 shadow-sm cursor-pointer
                                    {{ $req->status === 'completed' ? 'bg-emerald-100 text-emerald-800' : '' }}
                                    {{ $req->status === 'in_progress' ? 'bg-blue-100 text-blue-800' : '' }}
                                    {{ $req->status === 'pending' ? 'bg-amber-100 text-amber-800' : '' }}
                                    {{ $req->status === 'cancelled' ? 'bg-gray-100 text-gray-600' : '' }}">
                                <option value="pending" {{ $req->status === 'pending' ? 'selected' : '' }}>PENDING</option>
                                <option value="in_progress" {{ $req->status === 'in_progress' ? 'selected' : '' }}>IN_PROGRESS</option>
                                <option value="completed" {{ $req->status === 'completed' ? 'selected' : '' }}>COMPLETED</option>
                                <option value="cancelled" {{ $req->status === 'cancelled' ? 'selected' : '' }}>CANCELLED</option>
                            </select>
                        </td>
                        <td class="p-3">
                            <!-- WhatsApp Vendor Button -->
                            <a href="{{ $this->getWaVendorUrl($req->id) }}" target="_blank" 
                               class="px-2 py-1 bg-emerald-50 text-emerald-600 hover:bg-emerald-100 rounded-lg font-bold transition inline-flex items-center gap-1">
                                💬 WA Teknisi
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="p-8 text-center text-gray-400">Belum ada laporan kerusakan.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- 4. MODAL FORM LAPORKAN KERUSAKAN BARU -->
    @if($isModalOpen)
        <div class="fixed inset-0 z-50 overflow-y-auto bg-gray-900/60 backdrop-blur-sm flex justify-center items-center p-4">
            <div class="bg-white dark:bg-zinc-900 w-full max-w-lg rounded-2xl p-6 shadow-2xl border border-gray-200 dark:border-zinc-800">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-base font-bold text-gray-900 dark:text-white">Laporkan Kerusakan Baru</h3>
                    <button wire:click="$set('isModalOpen', false)" class="text-gray-400 hover:text-gray-600 text-sm">✕</button>
                </div>

                <form wire:submit.prevent="store" class="space-y-4 text-xs">
                    <!-- Judul -->
                    <div>
                        <label class="block font-semibold mb-1 text-gray-700 dark:text-gray-300">Judul Kerusakan</label>
                        <input type="text" wire:model="title" class="w-full rounded-lg border-gray-300 dark:border-zinc-700 dark:bg-zinc-800" placeholder="misal: AC Tidak Dingin / Kran Bocor">
                        @error('title') <span class="text-red-500 text-[10px]">{{ $message }}</span> @enderror
                    </div>

                    <!-- Kamar & Pelapor -->
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block font-semibold mb-1 text-gray-700 dark:text-gray-300">Kamar</label>
                            <select wire:model="room_id" class="w-full rounded-lg border-gray-300 dark:border-zinc-700 dark:bg-zinc-800">
                                <option value="">-- Pilih Kamar --</option>
                                @foreach($rooms as $room)
                                    <option value="{{ $room->id }}">Kamar {{ $room->room_number }}</option>
                                @endforeach
                            </select>
                            @error('room_id') <span class="text-red-500 text-[10px]">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="block font-semibold mb-1 text-gray-700 dark:text-gray-300">Pelapor (Penyewa)</label>
                            <select wire:model="customer_id" class="w-full rounded-lg border-gray-300 dark:border-zinc-700 dark:bg-zinc-800">
                                <option value="">-- Pengelola / None --</option>
                                @foreach($tenants as $tenant)
                                    <option value="{{ $tenant->id }}">{{ $tenant->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <!-- Deskripsi -->
                    <div>
                        <label class="block font-semibold mb-1 text-gray-700 dark:text-gray-300">Deskripsi Masalah</label>
                        <textarea wire:model="description" rows="3" class="w-full rounded-lg border-gray-300 dark:border-zinc-700 dark:bg-zinc-800" placeholder="Jelaskan detail kerusakan..."></textarea>
                        @error('description') <span class="text-red-500 text-[10px]">{{ $message }}</span> @enderror
                    </div>

                    <!-- Prioritas, Status, Cost -->
                    <div class="grid grid-cols-3 gap-3">
                        <div>
                            <label class="block font-semibold mb-1 text-gray-700 dark:text-gray-300">Prioritas</label>
                            <select wire:model="priority" class="w-full rounded-lg border-gray-300 dark:border-zinc-700 dark:bg-zinc-800">
                                <option value="low">Low</option>
                                <option value="medium">Medium</option>
                                <option value="high">High</option>
                                <option value="urgent">Urgent</option>
                            </select>
                        </div>
                        <div>
                            <label class="block font-semibold mb-1 text-gray-700 dark:text-gray-300">Status</label>
                            <select wire:model="status" class="w-full rounded-lg border-gray-300 dark:border-zinc-700 dark:bg-zinc-800">
                                <option value="pending">Pending</option>
                                <option value="in_progress">In Progress</option>
                                <option value="completed">Completed</option>
                            </select>
                        </div>
                        <div>
                            <label class="block font-semibold mb-1 text-gray-700 dark:text-gray-300">Biaya (Rp)</label>
                            <input type="number" wire:model="cost" class="w-full rounded-lg border-gray-300 dark:border-zinc-700 dark:bg-zinc-800" placeholder="0">
                        </div>
                    </div>

                    <!-- Upload Foto Bukti Kerusakan -->
                    <div>
                        <label class="block font-semibold mb-1 text-gray-700 dark:text-gray-300">Foto Bukti Kerusakan (Opsional)</label>
                        <input type="file" wire:model="issue_image" accept="image/*" class="w-full text-xs text-gray-500">
                        @error('issue_image') <span class="text-red-500 text-[10px]">{{ $message }}</span> @enderror
                    </div>

                    <!-- Action Buttons -->
                    <div class="flex items-center justify-end gap-2 pt-3 border-t border-gray-100 dark:border-zinc-800">
                        <button type="button" wire:click="$set('isModalOpen', false)" class="px-4 py-2 bg-gray-100 dark:bg-zinc-800 rounded-lg text-gray-600 dark:text-gray-300 font-semibold">
                            Batal
                        </button>
                        <button type="submit" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg font-semibold">
                            Simpan Laporan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>