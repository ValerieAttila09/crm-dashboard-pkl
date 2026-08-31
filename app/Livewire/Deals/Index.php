<?php
// /home/valerie09/Documents/PKL/Laravel/crm-dashboard/app/Livewire/Deals/Index.php

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
        $currentTeam = Auth::user()->currentTeam;
        $stages = ['lead', 'proposal', 'negotiation', 'won', 'lost'];
        
        // Filter Deal HANYA untuk Tim Aktif
        $dealsQuery = Deal::where('team_id', $currentTeam->id)
            ->with('customer')
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('title', 'like', '%' . $this->search . '%')
                      ->orWhereHas('customer', function ($cQuery) {
                          $cQuery->where('name', 'like', '%' . $this->search . '%');
                      });
                });
            })
            ->latest();

        $dealsGrouped = $dealsQuery->get()->groupBy('stage');

        // Hitung total nilai Rupiah per stage
        $stageTotals = [];
        foreach ($stages as $stg) {
            $stageTotals[$stg] = isset($dealsGrouped[$stg]) ? $dealsGrouped[$stg]->sum('amount') : 0;
        }

        // Dropdown Customer HANYA milik Tim Aktif
        $customers = Customer::where('team_id', $currentTeam->id)->orderBy('name')->get();

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

        $currentTeam = Auth::user()->currentTeam;

        if ($this->dealId) {
            // Mode Edit / Update (Pastikan deal milik tim aktif)
            $deal = Deal::where('team_id', $currentTeam->id)->findOrFail($this->dealId);
            $deal->update([
                'title' => $this->title,
                'customer_id' => $this->customer_id,
                'amount' => $this->amount,
                'stage' => $this->stage,
                'expected_close_date' => $this->expected_close_date ?: null,
            ]);
        } else {    
            // Mode Tambah Baru (Sertakan team_id)
            Deal::create([
                'team_id' => $currentTeam->id,
                'title' => $this->title,
                'customer_id' => $this->customer_id,
                'amount' => $this->amount,
                'stage' => $this->stage,
                'expected_close_date' => $this->expected_close_date ?: null,
            ]);
        }

        session()->flash('message', $this->dealId ? 'Deal berhasil diperbarui.' : 'Deal baru berhasil dibuat.');
        $this->closeModal();
    }

    public function edit($id)
    {
        $currentTeam = Auth::user()->currentTeam;
        $deal = Deal::where('team_id', $currentTeam->id)->findOrFail($id);
        
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
        $currentTeam = Auth::user()->currentTeam;
        $deal = Deal::where('team_id', $currentTeam->id)->find($dealId);
        
        if ($deal) {
            $deal->update(['stage' => $newStage]);
        }
    }

    public function delete($id)
    {
        if (!Auth::user()->isTeamAdmin()) {
            session()->flash('error', 'Hanya Admin yang diizinkan menghapus deal.');
            return;
        }

        $currentTeam = Auth::user()->currentTeam;
        Deal::where('team_id', $currentTeam->id)->where('id', $id)->delete();
        
        session()->flash('message', 'Deal berhasil dihapus.');
    }
}