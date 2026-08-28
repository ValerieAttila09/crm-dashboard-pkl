<?php

namespace App\Livewire\Customers;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Customer;
use Illuminate\Support\Facades\Auth;

class Index extends Component
{
    use WithPagination;

    public $search = '';
    public $isModalOpen = false;
    public $customerId = null;

    // Form Fields
    public $name, $email, $phone, $company, $status = 'lead';

    protected function rules()
    {
        return [
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:customers,email' . ($this->customerId ? ',' . $this->customerId : ''),
            'phone' => 'nullable|string|max:50',
            'company' => 'nullable|string|max:255',
            'status' => 'required|in:lead,prospect,customer,churned',
        ];
    }

    public function render()
    {
        $customers = Customer::where('name', 'like', '%' . $this->search . '%')
            ->orWhere('email', 'like', '%' . $this->search . '%')
            ->orWhere('company', 'like', '%' . $this->search . '%')
            ->latest()
            ->paginate(10);

        return view('livewire.customers.index', [
            'customers' => $customers
        ])->layout('layouts.app'); // Memastikan menggunakan layout Flux utama
    }

    public function create()
    {
        $this->resetInputFields();
        $this->isModalOpen = true;
    }

    public function openModal()
    {
        $this->isModalOpen = true;
    }

    public function closeModal()
    {
        $this->isModalOpen = false;
        $this->resetInputFields();
    }

    private function resetInputFields()
    {
        $this->customerId = null;
        $this->name = '';
        $this->email = '';
        $this->phone = '';
        $this->company = '';
        $this->status = 'lead';
        $this->resetValidation();
    }

    public function store()
    {
        $this->validate();

        Customer::updateOrCreate(
            ['id' => $this->customerId],
            [
                'name' => $this->name,
                'email' => $this->email,
                'phone' => $this->phone,
                'company' => $this->company,
                'status' => $this->status,
                'created_by' => Auth::id(),
            ]
        );

        session()->flash('message', $this->customerId ? 'Data pelanggan berhasil diperbarui.' : 'Pelanggan baru berhasil ditambahkan.');
        $this->closeModal();
    }

    public function edit($id)
    {
        $customer = Customer::findOrFail($id);
        $this->customerId = $id;
        $this->name = $customer->name;
        $this->email = $customer->email;
        $this->phone = $customer->phone;
        $this->company = $customer->company;
        $this->status = $customer->status;

        $this->openModal();
    }

    public function delete($id)
    {
        Customer::find($id)->delete();
        session()->flash('message', 'Data pelanggan berhasil dihapus.');
    }
}