<?php

namespace App\Livewire\Interactions;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Interaction;
use App\Models\Customer;

class Index extends Component
{
    use WithPagination;

    public $search = '';
    public $typeFilter = '';

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingTypeFilter()
    {
        $this->resetPage();
    }

    public function delete($id)
    {
        Interaction::find($id)?->delete();
        session()->flash('message', 'Log aktivitas berhasil dihapus.');
    }

    public function render()
    {
        $interactions = Interaction::with('customer')
            ->when($this->search, function ($query) {
                $query->where('notes', 'like', '%' . $this->search . '%')
                      ->orWhereHas('customer', function ($q) {
                          $q->where('name', 'like', '%' . $this->search . '%')
                            ->orWhere('company', 'like', '%' . $this->search . '%');
                      });
            })
            ->when($this->typeFilter, function ($query) {
                $query->where('type', $this->typeFilter);
            })
            ->latest()
            ->paginate(15);

        return view('livewire.interactions.index', [
            'interactions' => $interactions
        ])->layout('layouts.app');
    }
}