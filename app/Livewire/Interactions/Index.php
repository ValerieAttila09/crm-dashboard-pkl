<?php
// /home/valerie09/Documents/PKL/Laravel/crm-dashboard/app/Livewire/Interactions/Index.php

namespace App\Livewire\Interactions;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Interaction;
use App\Models\Customer;
use Illuminate\Support\Facades\Auth;

class Index extends Component
{
    use WithPagination;

    // Filter & Search
    public $search = '';
    public $typeFilter = '';
    public $dateFrom = '';
    public $dateTo = '';

    // Quick Add Modal Form
    public $isModalOpen = false;
    public $customer_id = '';
    public $type = 'call';
    public $notes = '';

    protected $rules = [
        'customer_id' => 'required|exists:customers,id',
        'type' => 'required|in:call,email,meeting,note',
        'notes' => 'required|string|min:3',
    ];

    public function updatingSearch() { $this->resetPage(); }
    public function updatingTypeFilter() { $this->resetPage(); }
    public function updatingDateFrom() { $this->resetPage(); }
    public function updatingDateTo() { $this->resetPage(); }

    public function openModal()
    {
        $this->reset(['customer_id', 'type', 'notes']);
        $this->resetValidation();
        $this->isModalOpen = true;
    }

    public function closeModal()
    {
        $this->isModalOpen = false;
    }

    public function store()
    {
        $this->validate();

        Interaction::create([
            'customer_id' => $this->customer_id,
            'type' => $this->type,
            'notes' => $this->notes,
            'user_id' => Auth::id(), // Menyimpan siapa yang mencatat
        ]);

        session()->flash('message', 'Log aktivitas baru berhasil ditambahkan!');
        $this->closeModal();
    }

    public function delete($id)
    {
        if (!Auth::user()->isTeamAdmin()) {
            session()->flash('error', 'Hanya Admin yang diizinkan menghapus log aktivitas.');
            return;
        }

        $currentTeam = Auth::user()->currentTeam;
        
        // Pastikan hanya bisa menghapus interaction milik tim aktif
        Interaction::whereHas('customer', function ($query) use ($currentTeam) {
            $query->where('team_id', $currentTeam->id);
        })->where('id', $id)->delete();

        session()->flash('message', 'Log aktivitas berhasil dihapus.');
    }

    public function render()
    {
        $currentTeam = Auth::user()->currentTeam;

        // Filter log aktivitas HANYA milik pelanggan di tim saat ini
        $interactions = Interaction::whereHas('customer', function ($query) use ($currentTeam) {
                $query->where('team_id', $currentTeam->id);
            })
            ->with(['customer', 'user'])
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('notes', 'like', '%' . $this->search . '%')
                      ->orWhereHas('customer', function ($cQuery) {
                          $cQuery->where('name', 'like', '%' . $this->search . '%')
                                 ->orWhere('company', 'like', '%' . $this->search . '%');
                      });
                });
            })
            ->when($this->typeFilter, function ($query) {
                $query->where('type', $this->typeFilter);
            })
            ->when($this->dateFrom, function ($query) {
                $query->whereDate('created_at', '>=', $this->dateFrom);
            })
            ->when($this->dateTo, function ($query) {
                $query->whereDate('created_at', '<=', $this->dateTo);
            })
            ->latest()
            ->paginate(15);

        // Dropdown Customer HANYA milik tim saat ini
        $customers = Customer::where('team_id', $currentTeam->id)->orderBy('name')->get();

        return view('livewire.interactions.index', [
            'interactions' => $interactions,
            'customers' => $customers,
        ])->layout('layouts.app');
    }
}