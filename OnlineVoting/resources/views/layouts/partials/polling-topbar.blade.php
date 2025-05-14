{{-- File: resources/views/layouts/partials/polling-topbar.blade.php --}}
<header class="top-bar">
    <button class="menu-toggle" aria-label="Toggle Menu">
        <i class="fas fa-bars"></i>
    </button>

    <div class="breadcrumbs">
        <a href="{{ route('po.dashboard') }}">Home</a>
        @hasSection('title')
            <span>@yield('title')</span> {{-- CSS adds the '/' before this span --}}
        @else
            <span>Dashboard</span>  {{-- CSS adds the '/' before this span --}}
        @endif
    </div>

    <div class="user-info">
        @if(Auth::check())
            {{-- To match the admin screenshot "Admin A", you need a similar structure --}}
            {{-- This is a simplified version. Your admin panel might have more complex HTML for the user menu/dropdown --}}
            <span class="user-avatar-circle-placeholder" style="display: inline-block; width: 25px; height: 25px; line-height: 25px; border-radius: 50%; background-color: #fff; color: #3c8dbc; text-align: center; font-weight: bold; font-size: 0.8em; margin-right: 8px; vertical-align: middle;">
                {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
            </span>
            <span style="vertical-align: middle; margin-right:15px;">{{ Auth::user()->name }}</span>

            <form method="POST" action="{{ route('logout') }}" style="display: inline;">
                @csrf
                <a href="{{ route('logout') }}"
                   onclick="event.preventDefault(); this.closest('form').submit();"
                   class="logout-btn" {{-- This class IS styled by your polling.css --}}
                   title="Logout">
                   <i class="fas fa-sign-out-alt"></i>
                   <span class="sr-only">Logout</span> {{-- For screen readers, can be visually hidden --}}
                </a>
            </form>
        @endif
    </div>
</header>