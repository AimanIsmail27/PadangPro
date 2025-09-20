<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - PadangPro</title>
    @vite('resources/css/app.css') <!-- if using Laravel Vite -->

    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate" />
    <meta http-equiv="Pragma" content="no-cache" />
    <meta http-equiv="Expires" content="0" />

</head>
<body class="h-screen flex">
    <!-- Left Section -->
    <div class="w-1/2 bg-gradient-to-br from-green-800 to-green-900 text-white flex flex-col relative p-10">
        <!-- PadangPro title at top-left -->
        <h1 class="text-3xl font-bold absolute text-[50px] top-10 left-30">PadangPro<span class="text-blue-400">.</span></h1>

        <!-- Slogan centered vertically -->
 <div class="absolute bottom-10 left-10 font-bold italic text-[90px] left-30" style="font-family: 'Poppins', sans-serif;">
        Reserve.<br>
        Play.<br>
        Repeat.
    </div>
    </div>

    <!-- Right Section -->
    <div class="w-1/2 flex justify-center items-center">
        <div class="w-full max-w-md p-8">
            <h2 class="text-3xl font-semibold mb-6">Login to your account</h2>

            <!-- Display general error message -->
            @if(session('error'))
                <p class="text-red-500 text-center mb-4">{{ session('error') }}</p>
            @endif
            
            <form action="{{ route('login') }}" method="POST" class="space-y-4">
                @csrf

                <div>
                    <label class="block text-xl font-medium mb-1">Email</label>
                    <input type="email" name="email" value="{{ old('email') }}" class="w-full border rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-400" placeholder="Enter your email">
                    @error('email')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-xl font-medium mb-1">Password</label>
                    <div class="relative">
                        <input type="password" name="password" class="w-full border rounded px-3 py-2 pr-10 focus:outline-none focus:ring-2 focus:ring-blue-400" placeholder="Enter your password">
                        <span class="absolute right-3 top-2.5 cursor-pointer text-gray-500">👁</span>
                    </div>
                    @error('password')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <button type="submit" class="text-l w-full bg-blue-600 text-white py-2 rounded hover:bg-blue-700 transition font-semibold">
                    Login
                </button>

                <a href="{{ route('login.google') }}" class="w-full border border-gray-300 bg-gray-100 py-2 rounded flex items-center justify-center space-x-2 hover:bg-gray-200 transition">
                    <img src="{{ asset('images/googlephoto.png') }}" alt="Google" class="w-10 h-6">    
                    <span class="text-l text-blue-600 font-semibold">Continue with Google</span>
                </a>

                <p class="text-center text-l mt-4">
                    New User? <a href="{{ route('register') }}" class="text-l text-blue-600 hover:underline font-semibold">Register Now</a>
                </p>
            </form>
        </div>
    </div>
</body>
</html>
