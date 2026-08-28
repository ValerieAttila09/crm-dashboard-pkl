<?php

namespace App\Livewire\Deals;

use Livewire\Component;
use App\Models\Deal;
use App\Models\Customer;
use Illuminate\Support\Facades\Auth;

class Index extends Component
{
    public $isModalOpen = false;
    public $dealId = null;
    public $search = '';

    // Form Fields
    public $title, $customer_id, $amount = 0, $stage = 'lead', $expected_close_date;

    protected $rules = [
        'title' => 'required|string|max:255',
        'customer_id' => 'required|exists:customers,id',
        'amount' => 'required|numeric|min:0',
        'stage' => 'required|in:lead,proposal,negotiation,won,lost',
        'expected_close_date' => 'nullable|date',
    ];

    public function render()
    {
        $stages = ['lead', 'proposal', 'negotiation', 'won', 'lost'];
        
        $dealsQuery = Deal::with('customer')
            ->when($this->search, function ($query) {
                $query->where('title', 'like', '%' . $this->search . '%')
                      ->orWhereHas('customer', function ($q) {
                          $q->where('name', 'like', '%' . $this->search . '%');
                      });
            })
            ->latest();

        $dealsGrouped = $dealsQuery->get()->groupBy('stage');

        // Hitung total nilai Rupiah per stage
        $stageTotals = [];
        foreach ($stages as $stg) {
            $stageTotals[$stg] = isset($dealsGrouped[$stg]) ? $dealsGrouped[$stg]->sum('amount') : 0;
        }

        $customers = Customer::orderBy('name')->get();

        return view('livewire.deals.index', [
            'stages' => $stages,
            'deals' => $dealsGrouped,
            'stageTotals' => $stageTotals,
            'customers' => $customers,
        ])->layout('layouts.app');
    }

    public function create()
    {
        $this->resetInputFields();
        $this->isModalOpen = true;
    }

    public function closeModal()
    {
        $this->isModalOpen = false;
        $this->resetInputFields();
    }

    private function resetInputFields()
    {
        $this->dealId = null;
        $this->title = '';
        $this->customer_id = '';
        $this->amount = 0;
        $this->stage = 'lead';
        $this->expected_close_date = null;
        $this->resetValidation();
    }

    public function store()
    {
        $this->validate();

        Deal::updateOrCreate(
            ['id' => $this->dealId],
            [
                'title' => $this->title,
                'customer_id' => $this->customer_id,
                'amount' => $this->amount,
                'stage' => $this->stage,
                'expected_close_date' => $this->expected_close_date ?: null,
                'created_by' => null, // Sesuai penyesuaian FK Supabase
            ]
        );

        session()->flash('message', $this->dealId ? 'Deal berhasil diperbarui.' : 'Deal baru berhasil dibuat.');
        $this->closeModal();
    }

    public function edit($id)
    {
        $deal = Deal::findOrFail($id);
        $this->dealId = $id;
        $this->title = $deal->title;
        $this->customer_id = $deal->customer_id;
        $this->amount = $deal->amount;
        $this->stage = $deal->stage;
        $this->expected_close_date = $deal->expected_close_date ? $deal->expected_close_date->format('Y-m-d') : null;

        $this->isModalOpen = true;
    }

    public function updateStage($dealId, $newStage)
    {
        $deal = Deal::find($dealId);
        if ($deal) {
            $deal->update(['stage' => $newStage]);
        }
    }

    public function delete($id)
    {
        Deal::find($id)?->delete();
        session()->flash('message', 'Deal berhasil dihapus.');
    }
}