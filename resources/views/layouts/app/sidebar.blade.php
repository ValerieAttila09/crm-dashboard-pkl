<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
    <head>
        @include('partials.head')
    </head>
    <body class="min-h-screen bg-white dark:bg-zinc-800">
        <flux:sidebar sticky collapsible="mobile" class="border-e border-zinc-200 bg-zinc-50 dark:border-zinc-700 dark:bg-zinc-900">
            <flux:sidebar.header>
                <x-app-logo :sidebar="true" href="{{ route('dashboard') }}" wire:navigate />
                <flux:sidebar.collapse class="lg:hidden" />
            </flux:sidebar.header>

            <livewire:team-switcher />

            <flux:sidebar.nav>
                <flux:sidebar.group :heading="__('Platform')" class="grid">
                    <flux:sidebar.item 
                        icon="home" 
                        :href="route('dashboard', ['current_team' => auth()->user()->currentTeam->slug])" 
                        :current="request()->routeIs('dashboard')" 
                        wire:navigate>
                        {{ __('Dashboard') }}
                    </flux:sidebar.item>

                    <flux:sidebar.item 
                        icon="users" 
                        :href="route('customers.index', ['current_team' => auth()->user()->currentTeam->slug])" 
                        :current="request()->routeIs('customers.index')" 
                        wire:navigate>
                        {{ __('Customers') }}
                    </flux:sidebar.item>

                    <flux:sidebar.item 
                        icon="currency-dollar" 
                        :href="route('deals.index', ['current_team' => auth()->user()->currentTeam->slug])" 
                        :current="request()->routeIs('deals.index')" 
                        wire:navigate>
                        {{ __('Deals') }}
                    </flux:sidebar.item>

                    <flux:sidebar.item 
                        icon="clock" 
                        :href="route('interactions.index', ['current_team' => auth()->user()->currentTeam->slug])" 
                        :current="request()->routeIs('interactions.index')" 
                        wire:navigate>
                        {{ __('Activities') }}
                    </flux:sidebar.item>

                    <flux:sidebar.item 
                        icon="check-circle" 
                        :href="route('tasks.index', ['current_team' => auth()->user()->currentTeam->slug])" 
                        :current="request()->routeIs('tasks.index')" 
                        wire:navigate>
                        {{ __('Tasks') }}
                    </flux:sidebar.item>

                    <flux:sidebar.item 
                        icon="calendar" 
                        :href="route('calendar.index', ['current_team' => auth()->user()->currentTeam->slug])" 
                        :current="request()->routeIs('calendar.index')" 
                        wire:navigate>
                        {{ __('Calendar') }}
                    </flux:sidebar.item>

                    <flux:sidebar.item 
                        icon="home-modern" 
                        :href="route('rooms.index', ['current_team' => auth()->user()->currentTeam->slug])" 
                        :current="request()->routeIs('rooms.index')" 
                        wire:navigate>
                        {{ __('Rooms & 360°') }}
                    </flux:sidebar.item>

                    <flux:sidebar.item 
                        icon="document-text" 
                        :href="route('leases.index', ['current_team' => auth()->user()->currentTeam->slug])" 
                        :current="request()->routeIs('leases.index')" 
                        wire:navigate>
                        {{ __('Leases & Billing') }}
                    </flux:sidebar.item>
                </flux:sidebar.group>
                <flux:sidebar.group :heading="__('Manage')" class="grid">
                    <flux:sidebar.item 
                        icon="cog-6-tooth" 
                        :href="route('team.settings', ['current_team' => auth()->user()->currentTeam->slug])" 
                        :current="request()->routeIs('team.settings')" 
                        wire:navigate>
                        {{ __('Team Settings') }}
                    </flux:sidebar.item>
                </flux:sidebar.group>
            </flux:sidebar.nav>

            <flux:spacer />

            <flux:sidebar.nav>
                <flux:sidebar.item icon="folder-git-2" href="https://github.com/laravel/livewire-starter-kit" target="_blank">
                    {{ __('Repository') }}
                </flux:sidebar.item>

                <flux:sidebar.item icon="book-open-text" href="https://laravel.com/docs/starter-kits#livewire" target="_blank">
                    {{ __('Documentation') }}
                </flux:sidebar.item>
            </flux:sidebar.nav>

            <x-desktop-user-menu class="hidden lg:block" :name="auth()->user()->name" />
        </flux:sidebar>

        <!-- Mobile User Menu -->
        <flux:header class="lg:hidden">
            <flux:sidebar.toggle class="lg:hidden" icon="bars-2" inset="left" />

            <flux:spacer />

            <flux:dropdown position="top" align="end">
                <flux:profile
                    :initials="auth()->user()->initials()"
                    icon-trailing="chevron-down"
                />

                <flux:menu>
                    <flux:menu.radio.group>
                        <div class="p-0 text-sm font-normal">
                            <div class="flex items-center gap-2 px-1 py-1.5 text-start text-sm">
                                <flux:avatar
                                    :name="auth()->user()->name"
                                    :initials="auth()->user()->initials()"
                                />

                                <div class="grid flex-1 text-start text-sm leading-tight">
                                    <flux:heading class="truncate">{{ auth()->user()->name }}</flux:heading>
                                    <flux:text class="truncate">{{ auth()->user()->email }}</flux:text>
                                </div>
                            </div>
                        </div>
                    </flux:menu.radio.group>

                    <flux:menu.separator />

                    <flux:menu.radio.group>
                        <flux:menu.item :href="route('profile.edit', ['current_team' => auth()->user()->currentTeam->slug])" icon="cog" wire:navigate>
                            {{ __('Settings') }}
                        </flux:menu.item>
                    </flux:menu.radio.group>

                    <flux:menu.separator />

                    <form method="POST" action="{{ route('logout') }}" class="w-full">
                        @csrf
                        <flux:menu.item
                            as="button"
                            type="submit"
                            icon="arrow-right-start-on-rectangle"
                            class="w-full cursor-pointer"
                            data-test="logout-button"
                        >
                            {{ __('Log out') }}
                        </flux:menu.item>
                    </form>
                </flux:menu>
            </flux:dropdown>
        </flux:header>

        {{ $slot }}

        <livewire:create-team-modal />

        @persist('toast')
            <flux:toast.group>
                <flux:toast />
            </flux:toast.group>
        @endpersist

        @fluxScripts
    </body>
</html>
