<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password - PadangPro</title>

    <script src="https://cdn.tailwindcss.com"></script>

    <style>
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
            <p class="text-2xl font-bold italic opacity-90">Set New Password</p>
            <p class="text-green-200 text-sm mt-2">Create a strong password to protect your account.</p>
        </div>
    </div>

    <div class="w-full md:w-1/2 flex justify-center items-start md:items-center -mt-24 md:mt-0 px-4 md:px-0 relative z-10">
        
        <div class="w-full max-w-md p-8 bg-white rounded-2xl shadow-2xl md:shadow-none md:rounded-none">
            
            <h2 class="text-2xl md:text-3xl font-semibold mb-2 text-gray-800">New Password</h2>
            <p class="text-gray-600 mb-6 text-sm">Please enter your new password below.</p>

            @if(session('error'))
                <div class="bg-red-50 border-l-4 border-red-500 text-red-700 p-3 rounded mb-6 text-sm">
                    {{ session('error') }}
                </div>
            @endif
            
            <form action="{{ route('password.update') }}" method="POST" class="space-y-5">
                @csrf
                <input type="hidden" name="token" value="{{ $token }}">
                <input type="hidden" name="email" value="{{ $email }}">

                <div>
                    <label class="block text-lg font-medium mb-1 text-gray-700">New Password</label>
                    <input type="password" name="password" required
                           class="w-full border border-gray-300 rounded px-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-400 transition-all" 
                           placeholder="At least 6 characters">
                    @error('password')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-lg font-medium mb-1 text-gray-700">Confirm New Password</label>
                    <input type="password" name="password_confirmation" required
                           class="w-full border border-gray-300 rounded px-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-400 transition-all" 
                           placeholder="Repeat your new password">
                </div>

                <button type="submit" class="w-full bg-blue-600 text-white py-3 rounded-lg hover:bg-blue-700 transition font-semibold text-lg shadow-md">
                    Update Password
                </button>
            </form>
        </div>
        
        <div class="h-10 md:hidden"></div>
    </div>

</body>
</html>
