<?php

use Livewire\Component;

new class extends Component
{
    //
};
?>

<div class="min-h-screen flex items-center justify-center p-4 bg-slate-50 dark:bg-zinc-900">
    <div class="bg-white dark:bg-zinc-800 rounded-2xl p-8 max-w-md w-full border border-gray-200 dark:border-zinc-700 text-center shadow-xl">
        @if(!$invitation || $invitation->isAccepted() || $invitation->isExpired())
            <div class="w-12 h-12 bg-red-100 text-red-600 rounded-full flex items-center justify-center mx-auto mb-4 font-bold text-xl">✕</div>
            <h2 class="text-lg font-bold text-gray-900 dark:text-white">Undangan Tidak Valid</h2>
            <p class="text-xs text-gray-500 mt-2">Undangan ini mungkin sudah kedaluwarsa atau pernah digunakan.</p>
            <a href="{{ route('dashboard') }}" class="mt-6 inline-block px-4 py-2 bg-indigo-600 text-white text-xs font-semibold rounded-lg">Kembali ke Dashboard</a>
        @else
            <div class="w-12 h-12 bg-indigo-100 text-indigo-600 rounded-full flex items-center justify-center mx-auto mb-4 font-bold text-xl">✉️</div>
            <h2 class="text-lg font-bold text-gray-900 dark:text-white">Undangan Bergabung</h2>
            <p class="text-xs text-gray-500 mt-2">
                Anda diundang untuk bergabung dengan tim <strong class="text-indigo-600 dark:text-indigo-400">{{ $invitation->team->name }}</strong> sebagai <strong class="uppercase">{{ $invitation->role->value ?? $invitation->role }}</strong>.
            </p>

            <button wire:click="accept" class="mt-6 w-full py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs rounded-xl shadow-md transition">
                Terima & Bergabung
            </button>
        @endif
    </div>
</div>