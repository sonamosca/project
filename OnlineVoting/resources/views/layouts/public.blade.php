<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <meta name="csrf-token" content="{{ csrf_token() }}"> {{-- Good practice for forms --}}

    <title>@yield('title', 'JNEC Online Voting System')</title> {{-- Dynamic Title --}}

    {{-- Font Awesome --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css"/>
    {{-- Link to your public CSS file --}}
    <link rel="stylesheet" href="{{ asset('css/public-style.css') }}"> {{-- We'll create this CSS file --}}

    @stack('styles') {{-- For page-specific CSS --}}
</head>
<body>

    <nav>
        {{-- Consider making this a separate partial if complex: @include('layouts.partials.public-nav') --}}
        <div class="nav-links">
            {{-- Replace .html with route() helpers once routes are defined --}}
            <a href="{{ url('/') }}">Home</a> {{-- Example, assumes 'home' route exists --}}
            <a href="{{-- route('instructions') --}}#instructions">Instructions</a>
            <a href="{{-- route('about') --}}#about">About</a>
            <a href="{{ route('contact') }}">Contact</a> {{-- Example, assumes 'contact' route exists --}}
        </div>
    </nav>

    {{-- Main content yield --}}
    @yield('content')

    <footer>
        {{-- Consider making this a partial: @include('layouts.partials.public-footer') --}}
        © {{ date('Y') }} JNEC Voting System. All Rights Reserved. |
        <a href="{{-- route('privacy') --}}#privacy">Privacy Policy</a>
    </footer>

    {{-- Include page-specific JS or general public JS --}}
    @stack('scripts')

</body>
</html>