<div class="p-6 space-y-6">
    <!-- Header Page -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-800 dark:text-white">Sales Pipeline (Deals)</h1>
            <p class="text-sm text-gray-500 dark:text-gray-400">Kelola alur transaksi dan estimasi pendapatan bisnis Anda</p>
        </div>
        <div class="flex items-center space-x-3">
            <!-- Search Bar -->
            <input type="text" wire:model.live.debounce.300ms="search" placeholder="Cari deal atau pelanggan..." class="px-3 py-2 text-sm border dark:border-zinc-700 rounded-lg dark:bg-zinc-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-indigo-500">
            
            <button wire:click="create()" class="px-4 py-2 bg-indigo-600 text-white font-medium text-sm rounded-lg hover:bg-indigo-700 transition">
                + Tambah Deal
            </button>
        </div>
    </div>

    @if (session()->has('message'))
        <div class="p-4 bg-emerald-100 border border-emerald-400 text-emerald-700 text-sm rounded-lg">
            {{ session('message') }}
        </div>
    @endif

    <!-- Kanban Board Columns Grid dengan Drag & Drop Alpine.js -->
    <div class="grid grid-cols-1 md:grid-cols-5 gap-4 overflow-x-auto pb-6">
        @foreach($stages as $stg)
            @php
                $stageColors = [
                    'lead' => 'border-t-blue-500',
                    'proposal' => 'border-t-purple-500',
                    'negotiation' => 'border-t-amber-500',
                    'won' => 'border-t-emerald-500',
                    'lost' => 'border-t-red-500',
                ];
            @endphp

            <div 
                x-data="{ draggingOver: false }"
                @dragover.prevent="draggingOver = true"
                @dragleave.prevent="draggingOver = false"
                @drop.prevent="
                    draggingOver = false;
                    let dealId = event.dataTransfer.getData('text/plain');
                    if (dealId) {
                        $wire.updateStage(dealId, '{{ $stg }}');
                    }
                "
                :class="draggingOver ? 'ring-2 ring-indigo-500 bg-indigo-50/20 dark:bg-indigo-900/10' : ''"
                class="bg-gray-50 dark:bg-zinc-900/60 p-4 rounded-xl border border-gray-200 dark:border-zinc-800 border-t-4 {{ $stageColors[$stg] ?? 'border-t-gray-400' }} min-w-[260px] flex flex-col justify-between transition"
            >
                <div>
                    <!-- Header Kolom Stage -->
                    <div class="mb-4 pb-2 border-b border-gray-200 dark:border-zinc-800 space-y-1">
                        <div class="flex justify-between items-center">
                            <h3 class="font-bold text-xs uppercase tracking-wider text-gray-700 dark:text-gray-300">
                                {{ ucfirst($stg) }}
                            </h3>
                            <span class="text-xs px-2 py-0.5 bg-gray-200 dark:bg-zinc-800 text-gray-600 dark:text-gray-400 rounded-full font-bold">
                                {{ isset($deals[$stg]) ? $deals[$stg]->count() : 0 }}
                            </span>
                        </div>
                        <p class="text-[11px] font-semibold text-emerald-600 dark:text-emerald-400">
                            Rp {{ number_format($stageTotals[$stg] ?? 0, 0, ',', '.') }}
                        </p>
                    </div>

                    <!-- List Kartu Deal -->
                    <div class="space-y-3 min-h-[150px]">
                        @forelse($deals[$stg] ?? [] as $deal)
                            <div 
                                draggable="true"
                                @dragstart="event.dataTransfer.setData('text/plain', '{{ $deal->id }}')"
                                class="bg-white dark:bg-zinc-800 p-4 rounded-lg shadow-sm border border-gray-100 dark:border-zinc-700/80 hover:shadow-md transition cursor-grab active:cursor-grabbing space-y-2 relative"
                            >
                                <div class="flex justify-between items-start">
                                    <h4 class="font-semibold text-gray-900 dark:text-white text-sm leading-snug">{{ $deal->title }}</h4>
                                    <div class="flex items-center space-x-2 text-xs">
                                        <button wire:click="edit('{{ $deal->id }}')" class="text-gray-400 hover:text-indigo-600">Edit</button>
                                        <button wire:click="delete('{{ $deal->id }}')" wire:confirm="Hapus deal ini?" class="text-gray-400 hover:text-red-600">&times;</button>
                                    </div>
                                </div>

                                <p class="text-xs text-indigo-600 dark:text-indigo-400 font-medium">
                                    {{ $deal->customer->name ?? 'Tanpa Pelanggan' }}
                                </p>

                                <div class="flex justify-between items-center pt-2 text-xs border-t border-gray-50 dark:border-zinc-700/50 text-gray-500">
                                    <span class="font-bold text-emerald-600 dark:text-emerald-400">
                                        Rp {{ number_format($deal->amount, 0, ',', '.') }}
                                    </span>
                                    @if($deal->expected_close_date)
                                        <span class="text-[10px] text-gray-400">
                                            {{ $deal->expected_close_date->format('d M Y') }}
                                        </span>
                                    @endif
                                </div>

                                <!-- Dropdown Pemindah Cepat -->
                                <div class="pt-1">
                                    <select wire:change="updateStage('{{ $deal->id }}', $event.target.value)" class="w-full text-[11px] border border-gray-200 dark:border-zinc-700 rounded bg-gray-50 dark:bg-zinc-900 text-gray-600 dark:text-gray-300 p-1 focus:outline-none">
                                        @foreach($stages as $s)
                                            <option value="{{ $s }}" {{ $deal->stage === $s ? 'selected' : '' }}>
                                                Stage: {{ ucfirst($s) }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-8 border-2 border-dashed border-gray-200 dark:border-zinc-800/80 rounded-lg text-xs text-gray-400">
                                Tarik kartu ke sini
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <!-- Modal Form Deal -->
    @if($isModalOpen)
    <div class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50">
        <div class="bg-white dark:bg-zinc-800 rounded-xl shadow-lg w-full max-w-md p-6 space-y-4">
            <h2 class="text-xl font-bold text-gray-800 dark:text-white">{{ $dealId ? 'Edit Deal' : 'Tambah Deal Baru' }}</h2>
            
            <form wire:submit.prevent="store" class="space-y-4">
                <div>
                    <label class="block text-xs font-medium text-gray-700 dark:text-gray-300">Judul Deal / Transaksi</label>
                    <input type="text" wire:model="title" placeholder="Misal: Lisensi Software CRM 1 Tahun" class="w-full mt-1 border dark:border-zinc-700 rounded-lg p-2 text-sm dark:bg-zinc-900 dark:text-white">
                    @error('title') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block text-xs font-medium text-gray-700 dark:text-gray-300">Pelanggan Terkait</label>
                    <select wire:model="customer_id" class="w-full mt-1 border dark:border-zinc-700 rounded-lg p-2 text-sm dark:bg-zinc-900 dark:text-white">
                        <option value="">-- Pilih Pelanggan --</option>
                        @foreach($customers as $c)
                            <option value="{{ $c->id }}">{{ $c->name }} ({{ $c->company ?? 'Perorangan' }})</option>
                        @endforeach
                    </select>
                    @error('customer_id') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block text-xs font-medium text-gray-700 dark:text-gray-300">Nilai Transaksi (Rp)</label>
                    <input type="number" wire:model="amount" class="w-full mt-1 border dark:border-zinc-700 rounded-lg p-2 text-sm dark:bg-zinc-900 dark:text-white">
                    @error('amount') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block text-xs font-medium text-gray-700 dark:text-gray-300">Tahap (Stage)</label>
                    <select wire:model="stage" class="w-full mt-1 border dark:border-zinc-700 rounded-lg p-2 text-sm dark:bg-zinc-900 dark:text-white">
                        <option value="lead">Lead</option>
                        <option value="proposal">Proposal</option>
                        <option value="negotiation">Negotiation</option>
                        <option value="won">Won (Berhasil)</option>
                        <option value="lost">Lost (Gagal)</option>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-medium text-gray-700 dark:text-gray-300">Estimasi Tanggal Closing</label>
                    <input type="date" wire:model="expected_close_date" class="w-full mt-1 border dark:border-zinc-700 rounded-lg p-2 text-sm dark:bg-zinc-900 dark:text-white">
                </div>

                <div class="flex justify-end space-x-2 pt-4">
                    <button type="button" wire:click="closeModal()" class="px-4 py-2 border rounded-lg text-xs font-medium text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-zinc-700">Batal</button>
                    <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-lg text-xs font-medium hover:bg-indigo-700">Simpan Deal</button>
                </div>
            </form>
        </div>
    </div>
    @endif
</div>