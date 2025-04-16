<header class="top-bar">
    <button class="menu-toggle" id="menu-toggle-btn">
        <i class="fas fa-bars"></i>
    </button>
    <div class="breadcrumbs">
        {{-- Use route() helper. Add dynamic breadcrumbs later if needed --}}
        <a href="{{ route('admin.dashboard') }}">Home</a> > <span>@yield('breadcrumb', 'Overview')</span>
    </div>
    <div class="user-info">
         {{-- Replace with dynamic user info if using Auth --}}
        <span>{{ Auth::user()->name ?? 'Admin User' }}</span> {{-- Example --}}
        {{-- Use asset() helper for images --}}
        <img src="{{ Auth::user()->profile_photo_url ?? asset('images/placeholder-user.png') }}" alt="User Image" class="user-image"> {{-- Example with profile photo or placeholder --}}

         {{-- Proper Logout Form --}}
         <form method="POST" action="{{ route('logout') }}" class="inline">
            @csrf
            <a href="{{ route('logout') }}"
               onclick="event.preventDefault(); this.closest('form').submit();"
               class="logout-btn">
               <i class="fas fa-sign-out-alt"></i> Logout
            </a>
        </form>
    </div>
</header>