<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}"> {{-- CSRF Token for security --}}

    <title>@yield('title', 'Admin Panel') - Voting System</title> {{-- Dynamic Title --}}

    <!-- Link to Font Awesome for Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <!-- Link to the Admin CSS file using asset() helper -->
    <link rel="stylesheet" href="{{ asset('css/admin.css') }}">

    @stack('styles') {{-- Placeholder for page-specific CSS --}}
</head>
<body>
    <div class="dashboard-container">
        <!-- Sidebar Navigation (Include Partial) -->
        @include('layouts.partials.admin-sidebar')

        <!-- Main Content Area -->
        <main class="main-content">
            <!-- Top Bar (Include Partial) -->
            @include('layouts.partials.admin-topbar')

            <!-- Page Content Area -->
            <section class="content-area" id="@yield('content-id', 'page-content')">
                 {{-- This is where the specific page content will go --}}
                @yield('content')
            </section>

        </main>
    </div>

    <!-- Basic JS for sidebar toggle using asset() helper -->
    <script src="{{ asset('js/admin-sidebar.js') }}"></script>
    @stack('scripts') {{-- Placeholder for page-specific JS --}}
</body>
</html>