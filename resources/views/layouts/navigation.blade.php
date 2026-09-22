{{-- Navigation Links Component --}}
<nav class="flex items-center gap-2">
    <a href="{{ route('events.index') }}" class="px-3 py-2 text-sm font-bold">
        🏃 Event Lari
    </a>

    @auth
        @if(auth()->user()->isRunner() || auth()->user()->isSuperAdmin())
            <a href="{{ route('runner.dashboard') }}" class="px-3 py-2 text-sm font-bold">
                🎫 Dashboard Tiket
            </a>
        @endif

        @if(auth()->user()->isOrganizer() || auth()->user()->isSuperAdmin())
            <a href="{{ route('organizer.events') }}" class="px-3 py-2 text-sm font-bold">
                🏢 Kelola Event
            </a>
        @endif

        @if(auth()->user()->isMarshal() || auth()->user()->isSuperAdmin())
            <a href="{{ route('marshal.scanner') }}" class="px-3 py-2 text-sm font-bold">
                📱 Scanner
            </a>
        @endif

        {{-- Item Menu Khusus Akun SuperAdmin --}}
        @if(Auth::user()->role === 'superadmin' || Auth::user()->role === 'SuperAdmin' || Auth::user()->isSuperAdmin())
            <a href="{{ route('admin.organizers.index') }}" class="px-3 py-2 text-sm font-bold text-brand-orange">
                👥 Kelola Organizer
            </a>
            <a href="{{ route('admin.dashboard') }}" class="px-3 py-2 text-sm font-bold">
                🛡️ Panel Admin
            </a>
        @endif
    @endauth
</nav>
