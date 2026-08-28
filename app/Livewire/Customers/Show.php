<?php

namespace App\Livewire\Customers;

use Livewire\Component;
use App\Models\Customer;

class Show extends Component
{
    public Customer $customer;

    public function mount($id)
    {
        $this->customer = Customer::with(['deals', 'interactions'])->findOrFail($id);
    }

    public function render()
    {
        return view('livewire.customers.show', [
            'customer' => $this->customer
        ])->layout('layouts.app');
    }
}