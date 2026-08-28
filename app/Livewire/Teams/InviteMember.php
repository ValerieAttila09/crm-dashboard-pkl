<?php

namespace App\Livewire\Teams;

use Livewire\Component;
use App\Models\User;
use App\Models\Team;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class InviteMember extends Component
{
    public $isModalOpen = false;
    public $email = '';
    public $role = 'member';

    protected $rules = [
        'email' => 'required|email',
        'role' => 'required|in:admin,member',
    ];

    public function openModal()
    {
        $this->reset(['email', 'role']);
        $this->resetValidation();
        $this->isModalOpen = true;
    }

    public function closeModal()
    {
        $this->isModalOpen = false;
    }

    public function invite()
    {
        $this->validate();

        $currentTeam = Auth::user()->currentTeam;

        if (!$currentTeam) {
            session()->flash('error', 'Tim aktif tidak ditemukan.');
            return;
        }

        // 1. Cek apakah user dengan email tersebut sudah terdaftar di aplikasi
        $user = User::where('email', $this->email)->first();

        if (!$user) {
            session()->flash('error', 'Pengguna dengan email ini belum terdaftar di aplikasi.');
            return;
        }

        // 2. Cek apakah user sudah menjadi anggota tim ini
        $alreadyMember = DB::table('team_members')
            ->where('team_id', $currentTeam->id)
            ->where('user_id', $user->id)
            ->exists();

        if ($alreadyMember) {
            session()->flash('error', 'Pengguna ini sudah menjadi anggota tim.');
            return;
        }

        // 3. Tambahkan user ke pivot table team_members
        DB::table('team_members')->insert([
            'team_id' => $currentTeam->id,
            'user_id' => $user->id,
            'role' => $this->role,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        session()->flash('message', "Berhasil menambahkan {$user->name} ke dalam tim sebagai " . ucfirst($this->role) . "!");
        $this->closeModal();
    }

    public function render()
    {
        return view('livewire.teams.invite-member');
    }
}