<?php

namespace App\Livewire\Teams;

use Livewire\Component;
use App\Models\TeamInvitation;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AcceptInvitation extends Component
{
    public $code;
    public $invitation;

    public function mount($code)
    {
        $this->code = $code;
        $this->invitation = TeamInvitation::where('code', $code)->with('team')->first();
    }

    public function accept()
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $user = Auth::user();

        if (!$this->invitation || $this->invitation->isAccepted() || $this->invitation->isExpired()) {
            session()->flash('error', 'Tautan undangan tidak valid atau sudah kedaluwarsa.');
            return redirect()->route('dashboard');
        }

        // 1. Masukkan pengguna ke pivot team_members
        DB::table('team_members')->insert([
            'team_id' => $this->invitation->team_id,
            'user_id' => $user->id,
            'role' => $this->invitation->role->value ?? $this->invitation->role,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // 2. Tandai undangan sebagai diterima
        $this->invitation->update([
            'accepted_at' => now(),
        ]);

        // 3. Set current_team user ke tim baru
        $user->update(['current_team_id' => $this->invitation->team_id]);

        session()->flash('message', "Selamat datang di tim {$this->invitation->team->name}!");
        return redirect()->route('dashboard', ['current_team' => $this->invitation->team->slug]);
    }

    public function render()
    {
        return view('livewire.teams.accept-invitation')->layout('layouts.app');
    }
}