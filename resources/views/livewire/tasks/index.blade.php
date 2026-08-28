<div class="p-6 space-y-6">
    <!-- Header Page -->
    <div>
        <h1 class="text-2xl font-bold text-gray-800 dark:text-white">Task & Follow-Up Reminders</h1>
        <p class="text-sm text-gray-500 dark:text-gray-400">Jadwalkan dan pantau agenda pengingat deals tim sales</p>
    </div>

    @if (session()->has('message'))
        <div class="p-3 bg-emerald-100 border border-emerald-400 text-emerald-700 text-xs rounded-lg">
            {{ session('message') }}
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Form Tambah Task -->
        <div class="bg-white dark:bg-zinc-900 p-5 rounded-xl border border-gray-100 dark:border-zinc-800 shadow-sm space-y-4 h-fit">
            <h3 class="font-bold text-base text-gray-800 dark:text-white">+ Buat Tugas Baru</h3>

            <form wire:submit.prevent="store" class="space-y-4">
                <div>
                    <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Judul / Agenda Follow-Up</label>
                    <input type="text" wire:model="title" placeholder="contoh: Telepon konfirmasi revisi proposal" class="w-full border dark:border-zinc-700 rounded-lg p-2.5 text-xs dark:bg-zinc-800 dark:text-white focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    @error('title') <span class="text-red-500 text-[11px] mt-1 block">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Tenggat Waktu (Due Date)</label>
                    <input type="date" wire:model="due_date" class="w-full border dark:border-zinc-700 rounded-lg p-2.5 text-xs dark:bg-zinc-800 dark:text-white focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    @error('due_date') <span class="text-red-500 text-[11px] mt-1 block">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Tautkan ke Deal (Opsional)</label>
                    <select wire:model="deal_id" class="w-full border dark:border-zinc-700 rounded-lg p-2.5 text-xs dark:bg-zinc-800 dark:text-white focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        <option value="">-- Tanpa Deal --</option>
                        @foreach($deals as $d)
                            <option value="{{ $d->id }}">{{ $d->title }} (Rp {{ number_format($d->amount, 0, ',', '.') }})</option>
                        @endforeach
                    </select>
                </div>

                <button type="submit" class="w-full py-2.5 bg-indigo-600 text-white font-medium text-xs rounded-lg hover:bg-indigo-700 transition">Simpan Tugas</button>
            </form>
        </div>

        <!-- Daftar Tasks -->
        <div class="lg:col-span-2 bg-white dark:bg-zinc-900 p-5 rounded-xl border border-gray-100 dark:border-zinc-800 shadow-sm space-y-4">
            <div class="flex items-center justify-between border-b dark:border-zinc-800 pb-3">
                <h3 class="font-bold text-base text-gray-800 dark:text-white">Daftar Agenda</h3>
                <div class="flex space-x-1 text-xs">
                    <button wire:click="$set('filter', 'all')" class="px-2.5 py-1 rounded-md {{ $filter === 'all' ? 'bg-indigo-100 text-indigo-700 dark:bg-indigo-900/40 dark:text-indigo-300 font-bold' : 'text-gray-500' }}">Semua</button>
                    <button wire:click="$set('filter', 'pending')" class="px-2.5 py-1 rounded-md {{ $filter === 'pending' ? 'bg-amber-100 text-amber-700 dark:bg-amber-900/40 dark:text-amber-300 font-bold' : 'text-gray-500' }}">Pending</button>
                    <button wire:click="$set('filter', 'completed')" class="px-2.5 py-1 rounded-md {{ $filter === 'completed' ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/40 dark:text-emerald-300 font-bold' : 'text-gray-500' }}">Selesai</button>
                </div>
            </div>

            <div class="divide-y divide-gray-100 dark:divide-zinc-800">
                @forelse($tasks as $t)
                    <div class="py-3 flex items-center justify-between gap-3">
                        <div class="flex items-center space-x-3">
                            <input type="checkbox" wire:click="toggleStatus('{{ $t->id }}')" {{ $t->is_completed ? 'checked' : '' }} class="w-4 h-4 text-indigo-600 rounded border-gray-300 focus:ring-indigo-500 cursor-pointer">
                            <div>
                                <p class="text-xs font-semibold {{ $t->is_completed ? 'line-through text-gray-400' : 'text-gray-800 dark:text-white' }}">{{ $t->title }}</p>
                                @if($t->deal)
                                    <span class="text-[11px] text-indigo-600 dark:text-indigo-400">@ Deal: {{ $t->deal->title }}</span>
                                @endif
                            </div>
                        </div>

                        <div class="flex items-center space-x-3">
                            @if($t->due_date)
                                <span class="text-[11px] px-2 py-0.5 rounded font-medium {{ $t->due_date->isPast() && !$t->is_completed ? 'bg-red-100 text-red-600 dark:bg-red-900/40 dark:text-red-400' : 'bg-gray-100 text-gray-600 dark:bg-zinc-800 dark:text-gray-400' }}">
                                    {{ $t->due_date->format('d M Y') }}
                                </span>
                            @endif
                            <button wire:click="delete('{{ $t->id }}')" wire:confirm="Hapus tugas ini?" class="text-gray-400 hover:text-red-600 text-xs">&times;</button>
                        </div>
                    </div>
                @empty
                    <p class="text-center py-8 text-xs text-gray-400">Belum ada agenda tugas.</p>
                @endforelse
            </div>
        </div>
    </div>
</div>