<?php

namespace App\Observers;

use App\Models\Deal;
use App\Models\Interaction;
use Illuminate\Support\Facades\Auth;

class DealObserver
{
    /**
     * Otomatis catat saat Deal baru dibuat.
     */
    public function created(Deal $deal): void
    {
        Interaction::create([
            'customer_id' => $deal->customer_id,
            'type' => 'note',
            'notes' => "SYSTEM: Deal baru '{$deal->title}' dibuat dengan nominal Rp " . number_format($deal->amount, 0, ',', '.') . " (Stage: " . ucfirst($deal->stage) . ").",
            'user_id' => Auth::id(),
        ]);
    }

    /**
     * Otomatis catat saat Stage Deal berubah (misal via Drag & Drop Kanban).
     */
    public function updated(Deal $deal): void
    {
        if ($deal->isDirty('stage')) {
            $oldStage = ucfirst($deal->getOriginal('stage'));
            $newStage = ucfirst($deal->stage);

            Interaction::create([
                'customer_id' => $deal->customer_id,
                'type' => 'note',
                'notes' => "SYSTEM: Perubahan Stage pada deal '{$deal->title}' dari [{$oldStage}] menjadi [{$newStage}].",
                'user_id' => Auth::id(),
            ]);
        }
    }

    /**
     * Otomatis catat saat Deal dihapus.
     */
    public function deleted(Deal $deal): void
    {
        Interaction::create([
            'customer_id' => $deal->customer_id,
            'type' => 'note',
            'notes' => "SYSTEM: Deal '{$deal->title}' telah dihapus dari sistem.",
            'user_id' => Auth::id(),
        ]);
    }

    /**
     * Handle the Deal "restored" event.
     */
    public function restored(Deal $deal): void
    {
        //
    }

    /**
     * Handle the Deal "force deleted" event.
     */
    public function forceDeleted(Deal $deal): void
    {
        //
    }
}
