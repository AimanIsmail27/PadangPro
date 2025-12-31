<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PadangPro - @yield('title')</title>

    <link rel="icon" type="image/png" href="{{ asset('images/logoPadang.png') }}">
<link rel="shortcut icon" type="image/png" href="{{ asset('images/logoPadang.png') }}">
    @vite('resources/css/landing.css')
</head>
<body>

    <!-- NAVBAR -->
    <nav class="bg-white shadow-md py-4">
        <div class="max-w-7xl mx-auto px-6 flex justify-between items-center">
            <a href="/" class="text-2xl font-bold text-blue-700">PadangPro</a>
            <div class="space-x-6">
                <a href="/" class="text-gray-700 hover:text-blue-700">Home</a>
                <a href="/about" class="text-gray-700 hover:text-blue-700">About</a>
                <a href="/login" class="text-gray-700 hover:text-blue-700">Login</a>
            </div>
        </div>
    </nav>

    <!-- PAGE CONTENT -->
    <main class="min-h-screen">
        @yield('content')
    </main>

    <!-- FOOTER -->
    <footer class="bg-gray-900 text-white py-6 mt-12">
        <div class="max-w-7xl mx-auto px-6 text-center">
            <p class="text-sm">&copy; {{ date('Y') }} PadangPro. All rights reserved.</p>
        </div>
    </footer>
</body>
</html>
