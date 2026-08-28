<?php

namespace App\Observers;

use App\Models\Task;
use App\Models\Interaction;
use Illuminate\Support\Facades\Auth;

class TaskObserver
{
    /**
     * Otomatis catat saat Task baru dibuat.
     */
    public function created(Task $task): void
    {
        // Ambil customer_id dari relasi Deal (jika ada)
        $customerId = $task->deal?->customer_id;

        if ($customerId) {
            Interaction::create([
                'customer_id' => $customerId,
                'type' => 'note',
                'notes' => "SYSTEM: Tugas follow-up baru '{$task->title}' telah dijadwalkan (Due: " . ($task->due_date ? $task->due_date->format('d M Y') : '-') . ").",
                'user_id' => (string) Auth::id(),
            ]);
        }
    }

    /**
     * Otomatis catat saat status Task diperbarui (Completed / Pending).
     */
    public function updated(Task $task): void
    {
        $customerId = $task->deal?->customer_id;

        if ($customerId && $task->isDirty('is_completed')) {
            $statusText = $task->is_completed ? 'SELESAI' : 'PENDING (Dibatalkan Selesai)';

            Interaction::create([
                'customer_id' => $customerId,
                'type' => 'note',
                'notes' => "SYSTEM: Status tugas '{$task->title}' diubah menjadi [{$statusText}].",
                'user_id' => (string) Auth::id(),
            ]);
        }
    }

    /**
     * Otomatis catat saat Task dihapus.
     */
    public function deleted(Task $task): void
    {
        $customerId = $task->deal?->customer_id;

        if ($customerId) {
            Interaction::create([
                'customer_id' => $customerId,
                'type' => 'note',
                'notes' => "SYSTEM: Tugas follow-up '{$task->title}' telah dihapus dari agenda.",
                'user_id' => (string) Auth::id(),
            ]);
        }
    }
}