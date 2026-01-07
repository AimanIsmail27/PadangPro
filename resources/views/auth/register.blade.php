<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - PadangPro</title>

    <script src="https://cdn.tailwindcss.com"></script>

    <style>
        .combo-gradient {
            background-image:
                linear-gradient(to bottom right, #15803d, #064e3b),
                linear-gradient(to right, rgba(21, 128, 61, 0.35), rgba(6, 78, 59, 0.55));
            background-blend-mode: overlay;
        }
    </style>
</head>

<body class="min-h-screen flex flex-col md:flex-row bg-gray-50 md:bg-white">

    <!-- LEFT PANEL -->
    <div class="w-full md:w-1/2 h-80 md:h-auto combo-gradient text-white flex flex-col relative p-8 md:p-10 justify-start shadow-xl md:shadow-none rounded-b-[3rem] md:rounded-none z-0">
        <h1 class="text-4xl md:text-[50px] font-bold absolute top-8 left-6 md:top-10 md:left-10">
            PadangPro<span class="text-blue-400 md:text-blue-500">.</span>
        </h1>

        <div class="hidden md:block absolute bottom-10 left-10 font-bold italic text-[86px] leading-tight" style="font-family: 'Poppins', sans-serif;">
            Reserve.<br>
            Play.<br>
            Repeat.
        </div>

        <div class="md:hidden flex flex-col justify-center h-full pb-10 text-center">
            <p class="text-2xl font-bold italic opacity-90">Create your account</p>
            <p class="text-green-200 text-sm mt-2">Start booking pitches and matching with players.</p>
        </div>
    </div>

    <!-- RIGHT PANEL -->
    <div class="w-full md:w-1/2 flex justify-center items-start md:items-center -mt-24 md:mt-0 px-4 md:px-0 relative z-10">
        <div class="w-full max-w-xl p-8 bg-white rounded-2xl shadow-2xl md:shadow-none md:rounded-none">

            <h2 class="text-2xl md:text-3xl font-semibold mb-2 text-gray-800">Register</h2>
            <p class="text-gray-600 mb-6 text-sm">Fill in your details to create your PadangPro account.</p>

            <!-- Validation Errors -->
            @if ($errors->any())
                <div class="bg-red-50 border-l-4 border-red-500 text-red-700 p-3 rounded mb-6 text-sm">
                    <p class="font-semibold mb-1">Please fix the following:</p>
                    <ul class="list-disc list-inside space-y-1">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <!-- Success/Error messages -->
            @if (session('success'))
                <div class="bg-green-50 border-l-4 border-green-500 text-green-700 p-3 rounded mb-6 text-sm">
                    {{ session('success') }}
                </div>
            @endif
            @if (session('error'))
                <div class="bg-red-50 border-l-4 border-red-500 text-red-700 p-3 rounded mb-6 text-sm">
                    {{ session('error') }}
                </div>
            @endif

            <form action="{{ route('register.submit') }}" method="POST" class="space-y-5">
                @csrf

                <!-- Account -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium mb-1 text-gray-700">Email</label>
                        <input type="email" name="user_Email" value="{{ old('user_Email') }}" required
                               class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-400 transition"
                               placeholder="name@example.com">
                    </div>

                    <div>
                        <label class="block text-sm font-medium mb-1 text-gray-700">Position</label>
                        <input type="text" name="customer_Position" value="{{ old('customer_Position') }}" required
                               class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-400 transition"
                               placeholder="e.g. Striker / Goalkeeper">
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium mb-1 text-gray-700">Password</label>
                        <input type="password" name="user_Password" required
                               class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-400 transition"
                               placeholder="At least 8 characters">
                    </div>

                    <div>
                        <label class="block text-sm font-medium mb-1 text-gray-700">Confirm Password</label>
                        <input type="password" name="user_Password_confirmation" required
                               class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-400 transition"
                               placeholder="Repeat password">
                    </div>
                </div>

                <!-- Profile -->
                <div>
                    <label class="block text-sm font-medium mb-1 text-gray-700">Full Name</label>
                    <input type="text" name="customer_FullName" value="{{ old('customer_FullName') }}" required
                           class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-400 transition"
                           placeholder="Your full name">
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium mb-1 text-gray-700">Age</label>
                        <input type="number" name="customer_Age" value="{{ old('customer_Age') }}" required min="1"
                               class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-400 transition"
                               placeholder="e.g. 21">
                    </div>

                    <div>
                        <label class="block text-sm font-medium mb-1 text-gray-700">Phone Number</label>
                        <input type="text" name="customer_PhoneNumber" value="{{ old('customer_PhoneNumber') }}" required
                               class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-400 transition"
                               placeholder="e.g. 01X-XXXXXXX">
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium mb-1 text-gray-700">Address</label>
                    <input type="text" name="customer_Address" value="{{ old('customer_Address') }}" required
                           class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-400 transition"
                           placeholder="Your address">
                </div>

                <button type="submit"
                        class="w-full bg-blue-600 text-white py-3 rounded-lg hover:bg-blue-700 transition font-semibold text-lg shadow-md">
                    Create Account
                </button>

                <p class="text-center text-sm text-gray-600">
                    Already have an account?
                    <a href="{{ route('login') }}" class="text-blue-600 hover:underline font-semibold">Back to Login</a>
                </p>
            </form>
        </div>

        <div class="h-10 md:hidden"></div>
    </div>

</body>
</html>
