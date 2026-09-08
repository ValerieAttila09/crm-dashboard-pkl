<?php

namespace App\Livewire\Teams;

use Livewire\Component;
use App\Models\TeamInvitation;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

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

        // 1. Cek apakah pengguna sudah menjadi anggota tim
        $alreadyMember = $currentTeam->members()->where('email', $this->email)->exists();
        if ($alreadyMember) {
            session()->flash('error', 'Pengguna dengan email ini sudah menjadi anggota tim.');
            return;
        }

        // 2. Cek apakah sudah ada undangan aktif (pending) untuk email ini
        $existingInvitation = TeamInvitation::where('team_id', $currentTeam->id)
            ->where('email', $this->email)
            ->whereNull('accepted_at')
            ->where('expires_at', '>', now())
            ->first();

        if ($existingInvitation) {
            session()->flash('error', 'Undangan untuk email ini sudah dikirimkan dan masih berlaku.');
            return;
        }

        // 3. Simpan Undangan Baru ke Tabel team_invitations
        $invitation = TeamInvitation::create([
            'team_id' => $currentTeam->id,
            'email' => $this->email,
            'role' => $this->role,
            'invited_by' => Auth::id(),
            'code' => Str::random(64),
            'expires_at' => now()->addDays(7), // Berlaku 7 hari
        ]);

        session()->flash('message', "Undangan berhasil dikirim ke {$this->email}! Kode Konfirmasi: {$invitation->code}");
        
        $this->dispatch('invitationSent');
        $this->closeModal();
    }

    public function render()
    {
        return view('livewire.teams.invite-member');
    }
}