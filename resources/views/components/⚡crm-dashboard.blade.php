<?php

use Livewire\Component;

new class extends Component
{
    //
};
?>

<div class="p-6 space-y-6">
    <h1 class="text-2xl font-bold text-gray-800">CRM Overview</h1>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div class="p-5 bg-white rounded-xl shadow-sm border border-gray-100">
            <p class="text-sm font-medium text-gray-500">Total Customers</p>
            <p class="text-3xl font-bold text-gray-900 mt-2">{{ $totalCustomers }}</p>
        </div>
        <div class="p-5 bg-white rounded-xl shadow-sm border border-gray-100">
            <p class="text-sm font-medium text-gray-500">Total Deals Value</p>
            <p class="text-3xl font-bold text-emerald-600 mt-2">Rp {{ number_format($totalDealsValue, 0, ',', '.') }}</p>
        </div>
    </div>

    <!-- Recent Customers Table -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100">
            <h2 class="text-lg font-semibold text-gray-800">Pelanggan Terbaru</h2>
        </div>
        <table class="w-full text-left text-sm text-gray-600">
            <thead class="bg-gray-50 text-gray-700">
                <tr>
                    <th class="px-6 py-3">Nama</th>
                    <th class="px-6 py-3">Email</th>
                    <th class="px-6 py-3">Perusahaan</th>
                    <th class="px-6 py-3">Status</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse ($recentCustomers as $customer)
                    <tr>
                        <td class="px-6 py-4 font-medium text-gray-900">{{ $customer->name }}</td>
                        <td class="px-6 py-4">{{ $customer->email }}</td>
                        <td class="px-6 py-4">{{ $customer->company ?? '-' }}</td>
                        <td class="px-6 py-4">
                            <span class="px-2 py-1 text-xs rounded-full bg-blue-100 text-blue-800 uppercase font-semibold">
                                {{ $customer->status }}
                            </span>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="px-6 py-4 text-center text-gray-400">Belum ada data pelanggan.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>