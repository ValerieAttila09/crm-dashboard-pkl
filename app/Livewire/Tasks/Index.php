<?php
// /home/valerie09/Documents/PKL/Laravel/crm-dashboard/app/Livewire/Tasks/Index.php

namespace App\Livewire\Tasks;

use Livewire\Component;
use App\Models\Task;
use App\Models\Deal;
use App\Notifications\TaskDueNotification;
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

        Auth::user()->notify(new TaskDueNotification($task, "Tugas baru '{$task->title}' telah dijadwalkan."));

        $this->reset(['title', 'deal_id', 'due_date']);
        session()->flash('message', 'Tugas follow-up berhasil ditambahkan.');
    }

    public function toggleStatus($id)
    {
        $currentTeam = Auth::user()->currentTeam;
        
        // Pastikan hanya bisa update task milik tim aktif
        $task = Task::whereHas('deal', function ($query) use ($currentTeam) {
            $query->where('team_id', $currentTeam->id);
        })->orWhereNull('deal_id')->find($id);

        if ($task) {
            $task->update([
                'is_completed' => !$task->is_completed,
            ]);
        }
    }

    public function delete($id)
    {
        if (!Auth::user()->isTeamAdmin()) {
            session()->flash('error', 'Hanya Admin yang diizinkan menghapus tugas.');
            return;
        }

        $currentTeam = Auth::user()->currentTeam;

        // Pastikan hanya bisa menghapus task milik tim aktif
        Task::whereHas('deal', function ($query) use ($currentTeam) {
            $query->where('team_id', $currentTeam->id);
        })->orWhereNull('deal_id')->where('id', $id)->delete();

        session()->flash('message', 'Tugas berhasil dihapus.');
    }

    public function render()
    {
        $currentTeam = Auth::user()->currentTeam;

        // Filter Task HANYA milik deal di tim saat ini
        $tasks = Task::whereHas('deal', function ($query) use ($currentTeam) {
                $query->where('team_id', $currentTeam->id);
            })
            ->with('deal.customer')
            ->when($this->filter === 'pending', fn($q) => $q->where('is_completed', false))
            ->when($this->filter === 'completed', fn($q) => $q->where('is_completed', true))
            ->orderBy('due_date', 'asc')
            ->get();

        // Dropdown Deal HANYA milik tim saat ini
        $deals = Deal::where('team_id', $currentTeam->id)->orderBy('title')->get();

        return view('livewire.tasks.index', [
            'tasks' => $tasks,
            'deals' => $deals,
        ])->layout('layouts.app');
    }
}