{{-- File: resources/views/layouts/partials/polling-sidebar.blade.php --}}
<aside class="sidebar">
    <div class="sidebar-header">
        <i class="fas fa-vote-yea"></i>
        <span>VotingSystem</span>
    </div>
    <div class="sidebar-user">
         <i class="fas fa-user-clock"></i>
         <span>{{ Auth::user()->name ?? 'Polling Officer' }}</span>
    </div>
    <nav class="sidebar-nav">
        <ul>
            <li class="{{ request()->routeIs('po.dashboard') ? 'active' : '' }}">
                <a href="{{ route('po.dashboard') }}">
                    <i class="fas fa-tachometer-alt fa-fw"></i> <span>Dashboard</span>
                </a>
            </li>
            <li class="{{ request()->routeIs('po.selectEvent') ? 'active' : '' }}">
                <a href="{{ route('po.selectEvent') }}">
                    <i class="fas fa-calendar-check fa-fw"></i> <span>Primary Event</span>
                </a>
            </li>
            <li class="{{ request()->routeIs('po.candidates.view.index') ? 'active' : '' }}">
                <a href="{{ route('po.candidates.view.index') }}">
                    <i class="fas fa-users fa-fw"></i> <span>View Candidates</span>
                </a>
            </li>
             <li class="{{ request()->routeIs('po.selectEvent') || request()->routeIs('po.event.scan') ? 'active' : '' }}">
                <a href="{{ route('po.selectEvent') }}"> {{-- Select event first, then scan page for that event --}}
                    <i class="fas fa-qrcode fa-fw"></i> <span>Voter Scan</span>
                </a>
            </li>
        </ul>
    </nav>
</aside>