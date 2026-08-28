<div class="p-6 space-y-6">
    <!-- Header Dashboard -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-800 dark:text-white">CRM Analytics Dashboard</h1>
            <p class="text-sm text-gray-500 dark:text-gray-400">Ringkasan performa penjualan dan hubungan pelanggan</p>
        </div>
        <div class="flex items-center space-x-3">
            <!-- KOMPONEN UNDANG ANGGOTA TIM -->
            <livewire:teams.invite-member />

            <a href="{{ route('deals.index', ['current_team' => auth()->user()->currentTeam->slug]) }}" wire:navigate class="px-3 py-1.5 bg-gray-100 dark:bg-zinc-800 text-gray-700 dark:text-gray-200 font-medium text-xs rounded-lg hover:bg-gray-200 dark:hover:bg-zinc-700 transition">
                Kelola Deals &rarr;
            </a>
        </div>
    </div>

    <!-- Metric Cards Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- Card 1: Total Customers -->
        <div class="bg-white dark:bg-zinc-900 p-5 rounded-xl border border-gray-100 dark:border-zinc-800 shadow-sm space-y-2">
            <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Total Pelanggan</p>
            <div class="flex items-baseline justify-between">
                <span class="text-3xl font-bold text-gray-900 dark:text-white">{{ $totalCustomers }}</span>
                <span class="text-xs px-2 py-0.5 rounded bg-blue-100 text-blue-700 font-medium">Orang</span>
            </div>
        </div>

        <!-- Card 2: Total Pipeline Value -->
        <div class="bg-white dark:bg-zinc-900 p-5 rounded-xl border border-gray-100 dark:border-zinc-800 shadow-sm space-y-2">
            <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Total Nilai Pipeline</p>
            <div class="flex items-baseline justify-between">
                <span class="text-xl font-bold text-emerald-600 dark:text-emerald-400">
                    Rp {{ number_format($totalDealsValue, 0, ',', '.') }}
                </span>
            </div>
        </div>

        <!-- Card 3: Revenue Won -->
        <div class="bg-white dark:bg-zinc-900 p-5 rounded-xl border border-gray-100 dark:border-zinc-800 shadow-sm space-y-2">
            <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Pendapatan Realisasi (Won)</p>
            <div class="flex items-baseline justify-between">
                <span class="text-xl font-bold text-indigo-600 dark:text-indigo-400">
                    Rp {{ number_format($wonDealsValue, 0, ',', '.') }}
                </span>
            </div>
        </div>

        <!-- Card 4: Win Rate % -->
        <div class="bg-white dark:bg-zinc-900 p-5 rounded-xl border border-gray-100 dark:border-zinc-800 shadow-sm space-y-2">
            <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Tingkat Keberhasilan (Win Rate)</p>
            <div class="flex items-baseline justify-between">
                <span class="text-3xl font-bold text-amber-500">{{ $winRate }}%</span>
                <span class="text-xs text-gray-400">{{ $activeDealsCount }} Active Deals</span>
            </div>
        </div>
    </div>

    <!-- Content Split: Pipeline Summary & Recent Activity -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Breakdown per Stage -->
        <div class="lg:col-span-2 bg-white dark:bg-zinc-900 p-6 rounded-xl border border-gray-100 dark:border-zinc-800 shadow-sm space-y-4">
            <h3 class="font-semibold text-gray-800 dark:text-gray-200 text-sm border-b dark:border-zinc-800 pb-3">
                Ringkasan Nilai Transaksi per Stage
            </h3>

            <div class="space-y-3">
                @foreach($stageBreakdown as $stg => $val)
                    <div>
                        <div class="flex justify-between text-xs font-medium text-gray-600 dark:text-gray-300 mb-1">
                            <span class="uppercase font-bold">{{ $stg }}</span>
                            <span class="font-semibold text-emerald-600 dark:text-emerald-400">Rp {{ number_format($val, 0, ',', '.') }}</span>
                        </div>
                        <div class="w-full bg-gray-100 dark:bg-zinc-800 rounded-full h-2">
                            <div class="bg-indigo-600 h-2 rounded-full" style="width: {{ $totalDealsValue > 0 ? min(100, round(($val / $totalDealsValue) * 100)) : 0 }}%"></div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Recent Interactions / Activity Log -->
        <div class="bg-white dark:bg-zinc-900 p-6 rounded-xl border border-gray-100 dark:border-zinc-800 shadow-sm space-y-4">
            <h3 class="font-semibold text-gray-800 dark:text-gray-200 text-sm border-b dark:border-zinc-800 pb-3">
                Aktivitas CRM Terbaru
            </h3>

            <div class="space-y-3">
                @forelse($recentInteractions as $act)
                    <div class="text-xs space-y-1 border-b border-gray-50 dark:border-zinc-800/80 pb-2">
                        <div class="flex justify-between items-center">
                            <span class="font-semibold text-indigo-600 dark:text-indigo-400">{{ $act->customer->name ?? 'Pelanggan' }}</span>
                            <span class="text-[10px] text-gray-400">{{ $act->created_at->diffForHumans() }}</span>
                        </div>
                        <p class="text-gray-600 dark:text-gray-300 text-[11px]">{{ $act->notes }}</p>
                    </div>
                @empty
                    <p class="text-xs text-gray-400 text-center py-6">Belum ada aktivitas tercatat.</p>
                @endforelse
            </div>
        </div>
    </div>
</div>