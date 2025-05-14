<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Polling Station') - {{ config('app.name', 'Voting System') }}</title>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('css/polling.css') }}"> {{-- This IS your AdminLTE-like theme --}}

    @stack('styles')
</head>
<body> {{-- The CSS you provided targets 'body' for some base styles and layout. No extra wrapper needed here. --}}

    {{-- This partial will be <aside class="sidebar">...</aside> --}}
    @include('layouts.partials.polling-sidebar')

    {{-- This div will contain the top bar and the main page content area --}}
    <div class="main-content">
        @include('layouts.partials.polling-topbar') {{-- This partial will be <header class="top-bar">...</header> --}}
        <div class="content-area">
            {{-- Your CSS styles "h1" directly inside ".content-area" for the page title --}}
            <h1>@yield('title', 'Dashboard')</h1>
            @yield('content')
        </div>
    </div>

    @stack('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const menuToggle = document.querySelector('.menu-toggle'); // From your .top-bar
            const sidebar = document.querySelector('.sidebar');
            const mainContent = document.querySelector('.main-content'); // For mobile adjustments primarily

            if (menuToggle && sidebar && mainContent) {
                menuToggle.addEventListener('click', function () {
                    // Toggle for desktop collapsed state
                    sidebar.classList.toggle('collapsed');

                    // Toggle for mobile visibility (if you implement .mobile-visible class for overlay/slide-in)
                    // For now, the CSS handles the small screen toggle by just making .collapsed full width when visible
                    // if (window.innerWidth <= 768) {
                    //    sidebar.classList.toggle('mobile-visible'); // You'd need CSS for .mobile-visible
                    // }
                });
            }
        });
    </script>
</body>
</html>