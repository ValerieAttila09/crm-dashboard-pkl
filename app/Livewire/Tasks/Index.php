<?php

namespace App\Livewire\Tasks;

use Livewire\Component;
use App\Models\Task;
use App\Models\Deal;
use Illuminate\Support\Facades\Auth;

class Index extends Component
{
    public $title = '';
    public $deal_id = '';
    public $due_date = '';
    public $filter = 'all';

    protected $rules = [
        'title' => 'required|string|min:3',
        'due_date' => 'required|date',
        'deal_id' => 'nullable|exists:deals,id',
    ];

    public function store()
    {
        $this->validate();

        Task::create([
            'title' => $this->title,
            'due_date' => $this->due_date,
            'deal_id' => $this->deal_id ?: null,
            'is_completed' => false,
        ]);

        $this->reset(['title', 'deal_id', 'due_date']);
        session()->flash('message', 'Tugas follow-up berhasil ditambahkan.');
    }

    public function toggleStatus($id)
    {
        $task = Task::find($id);
        if ($task) {
            $task->update([
                'is_completed' => !$task->is_completed,
            ]);
        }
    }

    public function delete($id)
    {
        Task::find($id)?->delete();
        session()->flash('message', 'Tugas berhasil dihapus.');
    }

    public function render()
    {
        // Ambil task yang terhubung dengan deals di tim saat ini
        $tasks = Task::with('deal.customer')
            ->when($this->filter === 'pending', fn($q) => $q->where('is_completed', false))
            ->when($this->filter === 'completed', fn($q) => $q->where('is_completed', true))
            ->orderBy('due_date', 'asc')
            ->get();

        $deals = Deal::orderBy('title')->get();

        return view('livewire.tasks.index', [
            'tasks' => $tasks,
            'deals' => $deals,
        ])->layout('layouts.app');
    }
}