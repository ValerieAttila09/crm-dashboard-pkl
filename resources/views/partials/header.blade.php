@php
    $teamSlug = auth()->user()?->currentTeam?->slug;
    $routeName = request()->route()?->getName();
    $currentPageTitle = $title ?? 'Dashboard';

    $breadcrumbs = collect([
        [
            'label' => __('Home'),
            'url' => $teamSlug ? route('dashboard', ['current_team' => $teamSlug]) : route('dashboard'),
        ],
    ]);

    if ($routeName === 'dashboard') {
        $breadcrumbs[] = ['label' => __('Dashboard'), 'url' => null];
    } elseif ($routeName === 'customers.index') {
        $breadcrumbs[] = ['label' => __('Customers'), 'url' => null];
    } elseif ($routeName === 'customers.show') {
        $breadcrumbs[] = ['label' => __('Customers'), 'url' => $teamSlug ? route('customers.index', ['current_team' => $teamSlug]) : route('customers.index')];
        $breadcrumbs[] = ['label' => __('Customer Details'), 'url' => null];
    } elseif ($routeName === 'deals.index') {
        $breadcrumbs[] = ['label' => __('Deals'), 'url' => null];
    } elseif ($routeName === 'interactions.index') {
        $breadcrumbs[] = ['label' => __('Activities'), 'url' => null];
    } elseif ($routeName === 'tasks.index') {
        $breadcrumbs[] = ['label' => __('Tasks'), 'url' => null];
    } elseif ($routeName === 'calendar.index') {
        $breadcrumbs[] = ['label' => __('Calendar'), 'url' => null];
    } elseif ($routeName === 'team.settings') {
        $breadcrumbs[] = ['label' => __('Team Settings'), 'url' => null];
    } elseif ($routeName === 'profile.edit') {
        $breadcrumbs[] = ['label' => __('Settings'), 'url' => null];
    } else {
        $breadcrumbs[] = ['label' => __($currentPageTitle), 'url' => null];
    }
@endphp

<flux:header class="border-b border-zinc-200 bg-white dark:border-zinc-700 dark:bg-zinc-900 px-6 py-3 flex items-center justify-between">
    <!-- Toggle Sidebar Mobile -->
    <div class="flex items-center gap-4">
        <flux:sidebar.toggle class="lg:hidden" icon="bars-2" inset="left" />

        <livewire:global-search class="lg:hidden"/>

        <div class="flex min-w-0 flex-col gap-1">
            <nav aria-label="Breadcrumb" class="hidden items-center gap-1 text-sm text-zinc-500 dark:text-zinc-400 sm:flex">
                @foreach ($breadcrumbs as $breadcrumb)
                    @if (! $loop->last)
                        <a href="{{ $breadcrumb['url'] ?? '#' }}" class="truncate transition hover:text-zinc-700 dark:hover:text-zinc-200">
                            {{ $breadcrumb['label'] }}
                        </a>
                        <span class="text-zinc-400 dark:text-zinc-500">/</span>
                    @else
                        <span class="truncate font-medium text-zinc-800 dark:text-zinc-100">
                            {{ $breadcrumb['label'] }}
                        </span>
                    @endif
                @endforeach
            </nav>
        </div>
    </div>

    <!-- Right Side Tools: Notifications & Profile -->
    <div class="flex items-center gap-4">
        <!-- Livewire Notification Bell Component -->
        <livewire:notification-bell />

        <div class="h-5 w-px bg-zinc-200 dark:bg-zinc-700"></div>

        <!-- User Profile Dropdown -->
        <flux:dropdown position="top" align="end">
            <button class="flex items-center gap-3 focus:outline-none">
                <flux:avatar
                    :name="auth()->user()->name"
                    :initials="auth()->user()->initials()"
                    class="size-8"
                />
                <div class="hidden md:grid text-start text-xs leading-tight">
                    <span class="font-semibold text-zinc-800 dark:text-zinc-100 truncate">{{ auth()->user()->name }}</span>
                    <span class="text-zinc-500 dark:text-zinc-400 truncate">{{ auth()->user()->currentTeam->name ?? 'No Team' }}</span>
                </div>
                <flux:icon icon="chevron-down" class="size-4 text-zinc-400" />
            </button>

            <flux:menu class="w-48">
                <div class="p-2 text-xs border-b border-zinc-100 dark:border-zinc-700">
                    <p class="font-semibold text-zinc-800 dark:text-zinc-100">{{ auth()->user()->name }}</p>
                    <p class="text-zinc-500 truncate">{{ auth()->user()->email }}</p>
                </div>

                <flux:menu.item :href="route('profile.edit', ['current_team' => auth()->user()->currentTeam->slug])" icon="cog" wire:navigate>
                    {{ __('Settings') }}
                </flux:menu.item>

                <flux:menu.separator />

                <form method="POST" action="{{ route('logout') }}" class="w-full">
                    @csrf
                    <flux:menu.item
                        as="button"
                        type="submit"
                        icon="arrow-right-start-on-rectangle"
                        class="w-full text-red-600 dark:text-red-400 cursor-pointer"
                    >
                        {{ __('Log out') }}
                    </flux:menu.item>
                </form>
            </flux:menu>
        </flux:dropdown>
    </div>
</flux:header>