<?php
// /home/valerie09/Documents/PKL/Laravel/crm-dashboard/app/Livewire/Customers/Interactions.php

namespace App\Livewire\Customers;

use Livewire\Component;
use App\Models\Interaction;
use Illuminate\Support\Facades\Auth;

class Interactions extends Component
{
    public $customerId;
    public $type = 'note';
    public $notes = '';

    protected $rules = [
        'type' => 'required|in:call,email,meeting,note',
        'notes' => 'required|string|min:3',
    ];

    public function mount($customerId)
    {
        $this->customerId = $customerId;
    }

    public function store()
    {
        $this->validate();

        Interaction::create([
            'customer_id' => $this->customerId,
            'type' => $this->type,
            'notes' => $this->notes,
            'created_by' => null, // Sesuai penyesuaian FK Supabase sebelumnya
        ]);

        $this->reset(['type', 'notes']);
        session()->flash('interaction_message', 'Aktivitas berhasil dicatat.');
    }

    public function delete($id)
    {
        Interaction::find($id)?->delete();
    }

    public function render()
    {
        $interactions = Interaction::where('customer_id', $this->customerId)
            ->latest()
            ->get();

        return view('livewire.customers.interactions', [
            'interactions' => $interactions
        ]);
    }
}