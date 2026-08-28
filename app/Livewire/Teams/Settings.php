<?php

namespace App\Livewire\Teams;

use Livewire\Component;
use App\Models\Team;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class Settings extends Component
{
    public function updateRole($userId, $newRole)
    {
        if (!Auth::user()->isTeamAdmin()) {
            session()->flash('error', 'Hanya Admin yang dapat mengubah role anggota.');
            return;
        }

        $currentTeam = Auth::user()->currentTeam;

        // Cegah mengubah role owner utama tim
        if ($currentTeam->user_id === $userId || $currentTeam->owner_id === $userId) {
            session()->flash('error', 'Role pemilik tim (Owner) tidak dapat diubah.');
            return;
        }

        DB::table('team_members')
            ->where('team_id', $currentTeam->id)
            ->where('user_id', $userId)
            ->update([
                'role' => $newRole,
                'updated_at' => now(),
            ]);

        session()->flash('message', 'Role anggota berhasil diperbarui.');
    }

    public function removeMember($userId)
    {
        if (!Auth::user()->isTeamAdmin()) {
            session()->flash('error', 'Hanya Admin yang dapat mengeluarkan anggota.');
            return;
        }

        $currentTeam = Auth::user()->currentTeam;

        // Cegah mengeluarkan owner utama tim
        if ($currentTeam->user_id === $userId || $currentTeam->owner_id === $userId) {
            session()->flash('error', 'Pemilik tim (Owner) tidak dapat dikeluarkan dari tim.');
            return;
        }

        DB::table('team_members')
            ->where('team_id', $currentTeam->id)
            ->where('user_id', $userId)
            ->delete();

        session()->flash('message', 'Anggota berhasil dikeluarkan dari tim.');
    }

    public function render()
    {
        $currentTeam = Auth::user()->currentTeam;

        // Ambil daftar anggota tim dari tabel pivot team_members
        $members = DB::table('team_members')
            ->join('users', 'team_members.user_id', '=', 'users.id')
            ->where('team_members.team_id', $currentTeam->id)
            ->select('users.id', 'users.name', 'users.email', 'team_members.role', 'team_members.created_at as joined_at')
            ->get();

        return view('livewire.teams.settings', [
            'team' => $currentTeam,
            'members' => $members,
        ])->layout('layouts.app');
    }
}