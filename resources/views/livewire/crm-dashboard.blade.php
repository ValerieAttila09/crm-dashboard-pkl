<div class="p-6 space-y-6">
    <!-- Header Dashboard -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-800 dark:text-white">CRM Analytics Dashboard</h1>
            <p class="text-sm text-gray-500 dark:text-gray-400">Ringkasan visual statistik penjualan dan pipeline</p>
        </div>
        <div class="flex items-center space-x-3">
            <livewire:teams.invite-member />
            <a href="{{ route('deals.index', ['current_team' => auth()->user()->currentTeam->slug]) }}" wire:navigate class="px-3 py-1.5 bg-indigo-600 text-white font-medium text-xs rounded-lg hover:bg-indigo-700 transition">
                Kelola Pipeline &rarr;
            </a>
        </div>
    </div>

    <!-- Container Grid Chart.js -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Line Chart: Trend Penjualan Won -->
        <div class="lg:col-span-2 bg-white dark:bg-zinc-900 p-5 rounded-xl border border-gray-100 dark:border-zinc-800 shadow-sm space-y-4">
            <h3 class="font-bold text-sm text-gray-800 dark:text-white flex items-center gap-2">
                <span class="w-2 h-2 rounded-full bg-indigo-500"></span> Trend Revenue Deals (Won)
            </h3>
            <div class="h-64 relative">
                <canvas id="revenueTrendChart"></canvas>
            </div>
        </div>

        <!-- Doughnut Chart: Proporsi Stage Deals -->
        <div class="bg-white dark:bg-zinc-900 p-5 rounded-xl border border-gray-100 dark:border-zinc-800 shadow-sm space-y-4">
            <h3 class="font-bold text-sm text-gray-800 dark:text-white flex items-center gap-2">
                <span class="w-2 h-2 rounded-full bg-purple-500"></span> Distribusi Stage Pipeline
            </h3>
            <div class="h-64 relative flex items-center justify-center">
                <canvas id="stageDistributionChart"></canvas>
            </div>
        </div>
    </div>
</div>

<!-- Import Chart.js CDN -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
    let revenueChart = null;
    let stageChart = null;

    function renderCrmCharts() {
        const ctxRevenue = document.getElementById('revenueTrendChart')?.getContext('2d');
        const ctxStage = document.getElementById('stageDistributionChart')?.getContext('2d');

        if (!ctxRevenue || !ctxStage) return;

        // Hapus chart lama jika sudah ada
        if (revenueChart) revenueChart.destroy();
        if (stageChart) stageChart.destroy();

        revenueChart = new Chart(ctxRevenue, {
            type: 'line',
            data: {
                labels: @json($monthsLabels),
                datasets: [{
                    label: 'Total Revenue (Rp)',
                    data: @json($revenueData),
                    borderColor: '#6366f1',
                    backgroundColor: 'rgba(99, 102, 241, 0.1)',
                    fill: true,
                    tension: 0.4,
                    borderWidth: 3,
                    pointRadius: 4,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: {
                    y: { beginAtZero: true, grid: { color: 'rgba(200, 200, 200, 0.1)' } },
                    x: { grid: { display: false } }
                }
            }
        });

        stageChart = new Chart(ctxStage, {
            type: 'doughnut',
            data: {
                labels: @json($stageLabels),
                datasets: [{
                    data: @json($stageChartData),
                    backgroundColor: ['#60a5fa', '#a78bfa', '#f59e0b', '#10b981', '#ef4444'],
                    borderWidth: 0,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { position: 'bottom', labels: { boxWidth: 12, font: { size: 11 } } }
                },
                cutout: '70%'
            }
        });
    }

    // Eksekusi saat pertama kali dimuat & saat navigasi Livewire
    document.addEventListener('DOMContentLoaded', renderCrmCharts);
    document.addEventListener('livewire:navigated', renderCrmCharts);
    setTimeout(renderCrmCharts, 100);
</script>