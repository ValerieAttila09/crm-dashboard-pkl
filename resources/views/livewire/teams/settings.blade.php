<?php

use Livewire\Component;

new class extends Component
{
    //
};
?>

<div class="p-6 space-y-6">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-800 dark:text-white">Pengaturan Tim & Hak Akses</h1>
            <p class="text-sm text-gray-500 dark:text-gray-400">Kelola anggota tim dan atur peran (role) operasional CRM Anda</p>
        </div>
        <div>
            <livewire:teams.invite-member />
        </div>
    </div>

    @if (session()->has('message'))
        <div class="p-3 bg-emerald-100 border border-emerald-400 text-emerald-700 text-xs rounded-lg">
            {{ session('message') }}
        </div>
    @endif

    @if (session()->has('error'))
        <div class="p-3 bg-red-100 border border-red-400 text-red-700 text-xs rounded-lg">
            {{ session('error') }}
        </div>
    @endif

    <!-- Card Informasi Tim -->
    <div class="bg-white dark:bg-zinc-900 p-6 rounded-xl border border-gray-100 dark:border-zinc-800 shadow-sm space-y-2">
        <h3 class="text-sm font-semibold text-gray-400 uppercase tracking-wider">Nama Tim Aktif</h3>
        <p class="text-xl font-bold text-gray-900 dark:text-white">{{ $team->name }}</p>
        <p class="text-xs text-gray-500 dark:text-gray-400">Slug: <code class="bg-gray-100 dark:bg-zinc-800 px-1.5 py-0.5 rounded text-indigo-600 dark:text-indigo-400">{{ $team->slug }}</code></p>
    </div>

    <!-- Tabel Anggota Tim -->
    <div class="bg-white dark:bg-zinc-900 rounded-xl border border-gray-100 dark:border-zinc-800 shadow-sm overflow-hidden space-y-4">
        <div class="p-6 border-b border-gray-100 dark:border-zinc-800">
            <h3 class="font-bold text-base text-gray-800 dark:text-white">Daftar Anggota Tim ({{ $members->count() }})</h3>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="border-b border-gray-100 dark:border-zinc-800 bg-gray-50/50 dark:bg-zinc-800/50 text-[11px] uppercase tracking-wider text-gray-400">
                        <th class="px-6 py-3">Nama Pengguna</th>
                        <th class="px-6 py-3">Email</th>
                        <th class="px-6 py-3">Tanggal Bergabung</th>
                        <th class="px-6 py-3">Peran (Role)</th>
                        <th class="px-6 py-3 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-zinc-800 text-xs">
                    @foreach($members as $member)
                        <tr class="hover:bg-gray-50 dark:hover:bg-zinc-800/50 transition">
                            <td class="px-6 py-4 font-semibold text-gray-900 dark:text-white">
                                {{ $member->name }}
                                @if($team->user_id === $member->id || $team->owner_id === $member->id)
                                    <span class="ml-2 px-2 py-0.5 bg-indigo-100 text-indigo-700 dark:bg-indigo-900/40 dark:text-indigo-300 text-[10px] rounded font-bold">OWNER</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-gray-600 dark:text-gray-300">{{ $member->email }}</td>
                            <td class="px-6 py-4 text-gray-400">{{ \Carbon\Carbon::parse($member->joined_at)->format('d M Y') }}</td>
                            <td class="px-6 py-4">
                                @if(auth()->user()->isTeamAdmin() && $team->user_id !== $member->id && $team->owner_id !== $member->id)
                                    <!-- Select Ubah Role (Hanya Admin) -->
                                    <select wire:change="updateRole({{ $member->id }}, $event.target.value)" class="text-xs border dark:border-zinc-700 rounded-lg p-1.5 dark:bg-zinc-800 dark:text-white focus:outline-none">
                                        <option value="member" {{ $member->role === 'member' ? 'selected' : '' }}>Member / Sales</option>
                                        <option value="admin" {{ $member->role === 'admin' ? 'selected' : '' }}>Admin</option>
                                    </select>
                                @else
                                    <span class="px-2.5 py-1 rounded-md text-[11px] font-bold uppercase tracking-wider {{ $member->role === 'admin' ? 'bg-purple-100 text-purple-700 dark:bg-purple-900/40 dark:text-purple-300' : 'bg-gray-100 text-gray-700 dark:bg-zinc-800 dark:text-gray-300' }}">
                                        {{ $member->role ?? 'Member' }}
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-right space-x-2">
                                @if(auth()->user()->isTeamAdmin() && $team->user_id !== $member->id && $team->owner_id !== $member->id)
                                    <button wire:click="removeMember({{ $member->id }})" wire:confirm="Keluarkan {{ $member->name }} dari tim ini?" class="text-red-600 hover:text-red-900 font-medium">
                                        Keluarkan
                                    </button>
                                @else
                                    <span class="text-gray-400 text-[11px]">-</span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>