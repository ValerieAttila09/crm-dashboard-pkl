<x-layouts::app.sidebar :title="$title ?? null">
    @include('partials.header')
    {{-- <livewire:notification-bell /> --}}
    <flux:main>
        {{ $slot }}
    </flux:main>
</x-layouts::app.sidebar>
