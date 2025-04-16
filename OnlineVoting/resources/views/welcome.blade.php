<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <title>JNEC Online Voting System</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap');

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'Poppins', sans-serif;
            background: #f0f8ff;
            height: 100vh;
            overflow: hidden;
        }
        .banner {
            position: relative;
            width: 100%;
            height: 100vh;
            background: url('{{ asset('images/JNEC.jpeg') }}') no-repeat center center/cover;
            animation: fadeIn 1.5s ease-out;
        }
        .overlay {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            text-align: center;
            color: white;
        }
        .logo-container {
            position: absolute;
            top: 100px;
            width: 100%;
            display: flex;
            justify-content: space-between;
            padding: 0 20px;
            z-index: 5;
        }
        .logo-container img {
            height: 150px;
            width: auto;
        }
        h1 {
            font-size: 40px;
            font-weight: 600;
            margin: 15px 0;
            text-transform: uppercase;
        }
        .nav-buttons {
            margin-top: 20px;
        }
        .nav-buttons a {
            background: #ffcc00;
            color: #333;
            padding: 14px 32px;
            font-weight: bold;
            text-decoration: none;
            border-radius: 30px;
            margin: 10px;
            transition: background 0.3s ease;
        }
        .nav-buttons a:hover {
            background: #ff9900;
        }
        .help {
            position: absolute;
            bottom: 20px;
            color: #ddd;
            font-size: 14px;
        }
        .help a {
            color: #ffcc00;
            text-decoration: none;
        }
        .help a:hover {
            text-decoration: underline;
        }
        nav {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            background: rgba(255, 255, 255, 0.9);
            padding: 10px 0;
            display: flex;
            justify-content: center;
            z-index: 10;
            font-size: 14px;
        }
        nav a {
            margin: 0 15px;
            color: #0056b3;
            text-decoration: none;
            font-weight: bold;
            text-transform: uppercase;
        }
        nav a:hover {
            color: #ff9900;
        }
        @keyframes fadeIn {
            from {opacity: 0;}
            to {opacity: 1;}
        }
    </style>
</head>
<body>
    <div class="banner">
        <!-- Navigation Bar -->
        <nav>
        <a href="{{ url('/') }}">Home</a>
            <a href="/instruction">Instructions</a>
            <a href="#">About</a>
            <a href="/contact">Contact</a>
            @if (Route::has('login'))
                @auth
                    <a href="{{ url('/dashboard') }}">Dashboard</a>
                @else
                    <a href="{{ route('login') }}">Login</a>
                    @if (Route::has('register'))
                        <a href="{{ route('register') }}">Register</a>
                    @endif
                @endauth
            @endif
        </nav>

        <!-- Logos -->
        <div class="logo-container">
            <img src="{{ asset('images/RUB_Logo.png') }}" alt="RUB Logo">
            <img src="{{ asset('images/jnec_logo.png') }}" alt="JNEC Logo">
        </div>

        <!-- Main Content -->
        <div class="overlay">
            <h1>Welcome to JNEC Online Voting System</h1>
            <p>
                    Dear Voters,<br>
                    Voting is your right and responsibility.<br>
                    Make your voice count and help shape the future of JNEC with a fair and transparent election.
                </p>
            <p class="help">
                Need Help? <a href="#">Read Instructions</a>
            </p>
        </div>
    </div>
</body>
</html>