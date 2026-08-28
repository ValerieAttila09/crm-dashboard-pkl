<?php

use App\Http\Middleware\EnsureTeamMembership;
use Illuminate\Support\Facades\Route;
use Laravel\Fortify\Features;

Route::prefix('{current_team}')
    ->middleware(['auth', 'verified', EnsureTeamMembership::class])
    ->group(function () {

        Route::redirect('settings', 'settings/profile');

        // Profile Settings
        Route::livewire('settings/profile', 'pages::settings.profile')->name('profile.edit');

        // Appearance Settings
        Route::livewire('settings/appearance', 'pages::settings.appearance')->name('appearance.edit');

        // Security Settings
        Route::livewire('settings/security', 'pages::settings.security')
            ->middleware(
                when(
                    Features::canManageTwoFactorAuthentication()
                    && Features::optionEnabled(Features::twoFactorAuthentication(), 'confirmPassword'),
                    ['password.confirm'],
                    [],
                ),
            )
            ->name('security.edit');

        // Teams Settings
        Route::livewire('settings/teams', 'pages::teams.index')->name('teams.index');
        Route::livewire('settings/teams/{team}', 'pages::teams.edit')->name('teams.edit');
    });

Route::get('.well-known/passkey-endpoints', function () {
    return response()->json([
        'enroll' => route('security.edit', ['current_team' => auth()->user()->currentTeam->slug]),
        'manage' => route('security.edit', ['current_team' => auth()->user()->currentTeam->slug]),
    ]);
})->name('well-known.passkeys');