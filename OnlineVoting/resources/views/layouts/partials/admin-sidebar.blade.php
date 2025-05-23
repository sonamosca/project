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
            <li><a href="{{route('admin.events.index')}}"><i class="fas fa-calendar-alt fa-fw"></i> Manage Events</a></li>
            {{-- Update href with route() when candidates route exists --}}
            <li class="{{ request()->routeIs('admin.candidates.*') ? 'active' : '' }}">
                <a href="{{ route('admin.candidates.index') }}"><i class="fas fa-user-tie fa-fw"></i> Manage Candidates</a>
            </li>
            {{-- Update href with route() when voters route exists --}}
            <li><a href="{{-- route('admin.voters.index') --}}#voters"><i class="fas fa-users fa-fw"></i> ManageVoters</a></li>
            <li class="{{ request()->routeIs('admin.programmes.*') ? 'active' : '' }}"> 
                 <a href="{{ route('admin.programmes.index') }}">
                     <i class="fas fa-graduation-cap fa-fw"></i> Manage Programmes
                 </a>
            </li>
            <li class="{{ request()->routeIs('admin.departments.*') ? 'active' : '' }}">
                <a href="{{ route('admin.departments.index') }}">
                    <i class="fas fa-chalkboard-teacher fa-fw"></i> Manage Departments
                </a>
            </li>
            <li class="{{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
                <a href="{{ route('admin.users.index') }}">
                    <i class="fas fa-users-cog"></i> Manage Users
                </a>
            </li>
        </ul>
        <p class="menu-header">Import Voters Data</p>
         <ul>
             {{-- Update href with route() when importing route exists --}}
            <li class="{{ request()->routeIs('admin.voters.manage.index') ? 'active' : '' }}">
                <a href="{{ route('admin.voters.manage.index') }}">
                    <i class="fas fa-file-import fa-fw"></i> Import Voters Data 
                </a>
            </li>
         </ul>

         <p class="menu-header">SETTINGS</p>
         <ul>
             {{-- Update href with route() when settings route exists --}}
            <li><a href="{{-- route('admin.settings.index') --}}#settings"><i class="fas fa-cogs fa-fw"></i> System Settings</a></li>
         </ul>
    </nav>
</aside>
