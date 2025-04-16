<aside class="sidebar">
    <div class="sidebar-header">
        <i class="fas fa-vote-yea"></i>
        <span>VotingSystem</span>
    </div>
    <div class="sidebar-user">
         <i class="fas fa-user-shield"></i>
         {{-- Replace with dynamic user info if using Auth --}}
         <span>{{ Auth::user()->name ?? 'ADMIN' }}</span> {{-- Example using Auth --}}
    </div>
    <nav class="sidebar-nav">
        <p class="menu-header">REPORTS</p>
        <ul>
            {{-- Use route() helper and check active state --}}
            <li class="{{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                <a href="{{ route('admin.dashboard') }}"><i class="fas fa-tachometer-alt fa-fw"></i> Dashboard</a>
            </li>
            {{-- Update href with route() when results route exists --}}
            <li><a href="{{-- route('admin.results.index') --}}#results"><i class="fas fa-poll fa-fw"></i> View Results</a></li>
        </ul>

        <p class="menu-header">MANAGE</p>
         <ul>
            {{-- Update href with route() when events route exists --}}
            <li><a href="{{url('manage_event_view')}}"><i class="fas fa-calendar-alt fa-fw"></i> Manage Events</a></li>
            {{-- Update href with route() when candidates route exists --}}
            <li><a href="{{-- route('admin.candidates.index') --}}#candidates"><i class="fas fa-user-tie fa-fw"></i> Manage Candidates</a></li>
            {{-- Update href with route() when voters route exists --}}
            <li><a href="{{-- route('admin.voters.index') --}}#voters"><i class="fas fa-users fa-fw"></i> ManageVoters</a></li>
        </ul>
        <p class="menu-header">Import Student Data</p>
         <ul>
             {{-- Update href with route() when importing route exists --}}
            <li><a href="{{-- route('admin.importing.index') --}}#importing"><i class="fas fa-file-import fa-fw"></i> Import Student Data</a></li>
         </ul>

         <p class="menu-header">SETTINGS</p>
         <ul>
             {{-- Update href with route() when settings route exists --}}
            <li><a href="{{-- route('admin.settings.index') --}}#settings"><i class="fas fa-cogs fa-fw"></i> System Settings</a></li>
         </ul>
    </nav>
</aside>