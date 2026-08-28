<?php

namespace App\Observers;

use App\Models\Customer;
use App\Models\Interaction;
use Illuminate\Support\Facades\Auth;

class CustomerObserver
{
    public function created(Customer $customer): void
    {
        Interaction::create([
            'customer_id' => $customer->id,
            'type' => 'note',
            'notes' => "SYSTEM: Pelanggan baru '{$customer->name}' (" . ($customer->company ?? 'Personal') . ") berhasil ditambahkan ke database.",
            'user_id' => Auth::id(),
        ]);
    }

    /**
     * Handle the Customer "updated" event.
     */
    public function updated(Customer $customer): void
    {
        //
    }

    /**
     * Handle the Customer "deleted" event.
     */
    public function deleted(Customer $customer): void
    {
        //
    }

    /**
     * Handle the Customer "restored" event.
     */
    public function restored(Customer $customer): void
    {
        //
    }

    /**
     * Handle the Customer "force deleted" event.
     */
    public function forceDeleted(Customer $customer): void
    {
        //
    }
}
