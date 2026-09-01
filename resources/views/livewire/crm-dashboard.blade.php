<div class="p-6 space-y-6">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Property & Living Management Dashboard</h1>
            <p class="text-xs text-gray-500 dark:text-gray-400">Ringkasan okupansi unit kamar, pendapatan sewa, dan statistik properti.</p>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('rooms.index', ['current_team' => auth()->user()->currentTeam->slug]) }}" class="px-3 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-semibold rounded-lg shadow" wire:navigate>
                + Kelola Unit Kamar
            </a>
            <a href="{{ route('leases.index', ['current_team' => auth()->user()->currentTeam->slug]) }}" class="px-3 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-semibold rounded-lg shadow" wire:navigate>
                + Kontrak Sewa
            </a>
        </div>
    </div>

    <!-- Metric Cards Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white dark:bg-zinc-800 p-4 rounded-xl border border-gray-200 dark:border-zinc-700 shadow-sm">
            <p class="text-xs font-semibold text-gray-500 dark:text-gray-400">Total Properti</p>
            <h3 class="text-2xl font-black text-gray-900 dark:text-white mt-1">{{ $totalProperties }}</h3>
            <span class="text-[10px] text-gray-400">Lokasi / Gedung terdaftar</span>
        </div>

        <div class="bg-white dark:bg-zinc-800 p-4 rounded-xl border border-gray-200 dark:border-zinc-700 shadow-sm">
            <p class="text-xs font-semibold text-gray-500 dark:text-gray-400">Total Unit Kamar</p>
            <h3 class="text-2xl font-black text-indigo-600 dark:text-indigo-400 mt-1">{{ $totalRooms }}</h3>
            <span class="text-[10px] text-gray-400">{{ $availableRooms }} Kamar Tersedia</span>
        </div>

        <div class="bg-white dark:bg-zinc-800 p-4 rounded-xl border border-gray-200 dark:border-zinc-700 shadow-sm">
            <p class="text-xs font-semibold text-gray-500 dark:text-gray-400">Tingkat Okupansi</p>
            <h3 class="text-2xl font-black text-emerald-600 dark:text-emerald-400 mt-1">{{ $occupancyRate }}%</h3>
            <span class="text-[10px] text-gray-400">{{ $occupiedRooms }} Unit Kamar Terisi</span>
        </div>

        <div class="bg-white dark:bg-zinc-800 p-4 rounded-xl border border-gray-200 dark:border-zinc-700 shadow-sm">
            <p class="text-xs font-semibold text-gray-500 dark:text-gray-400">Pendapatan Sewa Bulanan</p>
            <h3 class="text-2xl font-black text-gray-900 dark:text-white mt-1">Rp {{ number_format($monthlyRevenueTarget, 0, ',', '.') }}</h3>
            <span class="text-[10px] text-gray-400">Dari tagihan sewa yang lunas</span>
        </div>
    </div>

    <!-- Charts Section -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Line Chart Trend Pendapatan -->
        <div class="lg:col-span-2 bg-white dark:bg-zinc-800 p-5 rounded-xl border border-gray-200 dark:border-zinc-700 shadow-sm">
            <h3 class="text-sm font-bold text-gray-900 dark:text-white mb-4">Trend Pendapatan Sewa Properti</h3>
            <div class="h-64">
                <canvas id="revenueTrendChart"></canvas>
            </div>
        </div>

        <!-- Doughnut Chart Okupansi -->
        <div class="bg-white dark:bg-zinc-800 p-5 rounded-xl border border-gray-200 dark:border-zinc-700 shadow-sm">
            <h3 class="text-sm font-bold text-gray-900 dark:text-white mb-4">Distribusi Status Kamar</h3>
            <div class="h-64 relative">
                <canvas id="roomStatusChart"></canvas>
            </div>
        </div>
    </div>

   <!-- Chart.js CDN -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <script>
        document.addEventListener('livewire:navigated', function () {
            initPropertyCharts();
        });

        if (document.readyState === 'complete' || document.readyState === 'interactive') {
            initPropertyCharts();
        } else {
            document.addEventListener('DOMContentLoaded', initPropertyCharts);
        }

        function initPropertyCharts() {
            const revenueEl = document.getElementById('revenueTrendChart');
            const roomEl = document.getElementById('roomStatusChart');

            if (!revenueEl || !roomEl) return;

            let existingRevenueChart = Chart.getChart(revenueEl);
            if (existingRevenueChart) existingRevenueChart.destroy();

            let existingRoomChart = Chart.getChart(roomEl);
            if (existingRoomChart) existingRoomChart.destroy();

            // 1. Line Chart Trend Pendapatan
            new Chart(revenueEl.getContext('2d'), {
                type: 'line',
                data: {
                    labels: @json($monthsLabels),
                    datasets: [{
                        label: 'Pendapatan (Rp)',
                        data: @json($revenueData),
                        borderColor: '#6366f1',
                        backgroundColor: 'rgba(99, 102, 241, 0.1)',
                        fill: true,
                        tension: 0.3
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { legend: { display: false } }
                }
            });

            // 2. Doughnut Chart Status Kamar
            const rawRoomData = @json($stageChartData);
            const isAllZero = rawRoomData.every(val => val === 0);

            // Jika belum ada data kamar, berikan placeholder dummy 1 agar grafik lingkaran tetap dirender
            const chartData = isAllZero ? [1, 0, 0] : rawRoomData;
            const chartColors = isAllZero ? ['#e5e7eb', '#e5e7eb', '#e5e7eb'] : ['#10b981', '#6366f1', '#f59e0b'];

            new Chart(roomEl.getContext('2d'), {
                type: 'doughnut',
                data: {
                    labels: isAllZero ? ['Belum ada data kamar'] : @json($stageLabels),
                    datasets: [{
                        data: chartData,
                        backgroundColor: chartColors
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom'
                        }
                    }
                }
            });
        }
    </script>
</div>