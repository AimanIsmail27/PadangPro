<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - PadangPro</title>

    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>

    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate" />
    <meta http-equiv="Pragma" content="no-cache" />
    <meta http-equiv="Expires" content="0" />

    <style>
        /* 🌟 Custom combo gradient */
        .combo-gradient {
            background-image: 
                linear-gradient(to bottom right, #15803d, #064e3b), 
                linear-gradient(to right, rgba(21, 128, 61, 0.4), rgba(6, 78, 59, 0.6));
            background-blend-mode: overlay;
        }
    </style>
</head>
<body class="min-h-screen flex flex-col md:flex-row bg-gray-50 md:bg-white">

    <div class="w-full md:w-1/2 h-80 md:h-auto combo-gradient text-white flex flex-col relative p-8 md:p-10 justify-start md:justify-start shadow-xl md:shadow-none rounded-b-[3rem] md:rounded-none z-0">
        
        <h1 class="text-4xl md:text-[50px] font-bold absolute top-8 left-6 md:top-10 md:left-30">
            PadangPro<span class="text-blue-400 md:text-blue-500">.</span>
        </h1>

        <div class="hidden md:block absolute bottom-10 left-10 font-bold italic text-[90px] leading-tight left-30" style="font-family: 'Poppins', sans-serif;">
            Reserve.<br>
            Play.<br>
            Repeat.
        </div>
        
        <div class="md:hidden flex flex-col justify-center h-full pb-10 text-center">
            <p class="text-2xl font-bold italic opacity-90">Reserve. Play. Repeat.</p>
            <p class="text-green-200 text-sm mt-2">Your ultimate sports booking partner</p>
            <p class="text-green-200 text-sm mt-2">of Arena Owl Trafford, Bangi</p>
        </div>
    </div>

    <div class="w-full md:w-1/2 flex justify-center items-start md:items-center -mt-24 md:mt-0 px-4 md:px-0 relative z-10">
        
        <div class="w-full max-w-md p-8 bg-white rounded-2xl shadow-2xl md:shadow-none md:rounded-none">
            
            <h2 class="text-2xl md:text-3xl font-semibold mb-6 text-gray-800">Login to your account</h2>

            @if(session('error'))
                <div class="bg-red-50 border-l-4 border-red-500 text-red-700 p-3 rounded mb-6 text-sm">
                    {{ session('error') }}
                </div>
            @endif
            
            <form action="{{ route('login') }}" method="POST" class="space-y-5">
                @csrf

                <div>
                    <label class="block text-lg font-medium mb-1 text-gray-700">Email</label>
                    <input type="email" name="email" value="{{ old('email') }}" 
                           class="w-full border border-gray-300 rounded px-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-400 transition-all" 
                           placeholder="Enter your email">
                    @error('email')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
    <label class="block text-lg font-medium mb-1 text-gray-700">Password</label>
    <div class="relative">
        <input type="password" name="password" id="passwordInput" 
               class="w-full border border-gray-300 rounded px-4 py-3 pr-10 focus:outline-none focus:ring-2 focus:ring-blue-400 transition-all" 
               placeholder="Enter your password">
        <span onclick="togglePassword()" class="absolute right-3 top-3.5 cursor-pointer text-gray-400 hover:text-gray-600">
            <svg xmlns="https://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z" />
                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
            </svg>
        </span>
    </div>
    
    <div class="flex justify-end mt-2">
        <a href="{{ route('password.request') }}" class="text-sm font-semibold text-blue-600 hover:underline">
            Forgot Password?
        </a>
    </div>

    @error('password')
        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
    @enderror
</div>

                <button type="submit" class="w-full bg-blue-600 text-white py-3 rounded-lg hover:bg-blue-700 transition font-semibold text-lg shadow-md">
                    Login
                </button>

                <div class="relative flex py-2 items-center">
                    <div class="flex-grow border-t border-gray-200"></div>
                    <span class="flex-shrink-0 mx-4 text-gray-400 text-sm">OR</span>
                    <div class="flex-grow border-t border-gray-200"></div>
                </div>

                <a href="{{ route('login.google') }}" class="w-full border border-gray-300 bg-gray-50 text-gray-700 py-3 rounded-lg flex items-center justify-center space-x-3 hover:bg-gray-200 transition font-semibold shadow-sm">
                    <img src="{{ secure_asset('images/googlephoto.png') }}" alt="Google" class="w-5 h-5">    
                    <span class="text-base font-medium">Continue with Google</span>
                </a>

                <p class="text-center text-base mt-6 text-gray-600">
                    New User? <a href="{{ route('register') }}" class="text-blue-600 hover:underline font-semibold">Register Now</a>
                </p>
            </form>
        </div>
        
        <div class="h-10 md:hidden"></div>
    </div>

    <script>
        function togglePassword() {
            var x = document.getElementById("passwordInput");
            x.type = x.type === "password" ? "text" : "password";
        }
    </script>
</body>
</html>
