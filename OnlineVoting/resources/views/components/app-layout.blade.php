{{-- resources/views/components/app-layout.blade.php --}}
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>App Layout</title>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <script src="{{ asset('js/app.js') }}" defer></script>
</head>
<body>
    <div class="min-h-screen bg-gray-100">
        <!-- You can include a navbar or sidebar here -->

        <!-- Main content -->
        <main class="py-4">
            {{ $slot }}
        </main>
    </div>
</body>
</html>
