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

        /* ✅ same overlays as login page */
        .dot-texture {
            background-image: radial-gradient(rgba(255,255,255,0.10) 1px, transparent 1px);
            background-size: 18px 18px;
        }

        .pitch-pattern {
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='520' height='520' viewBox='0 0 520 520'%3E%3Cg fill='none' stroke='rgba(255,255,255,0.16)' stroke-width='2'%3E%3Crect x='26' y='26' width='468' height='468' rx='18'/%3E%3Cline x1='260' y1='26' x2='260' y2='494'/%3E%3Ccircle cx='260' cy='260' r='64'/%3E%3Ccircle cx='260' cy='260' r='6' fill='rgba(255,255,255,0.16)' stroke='none'/%3E%3Crect x='26' y='146' width='96' height='228' rx='10'/%3E%3Crect x='398' y='146' width='96' height='228' rx='10'/%3E%3Crect x='26' y='196' width='46' height='128' rx='10'/%3E%3Crect x='448' y='196' width='46' height='128' rx='10'/%3E%3C/g%3E%3C/svg%3E");
            background-size: 520px 520px;
            background-repeat: no-repeat;
            background-position: 85% 65%;
            opacity: .35;
        }

        /* ✅ same animation behaviour */
        @keyframes fadeSlideIn {
            from { opacity: 0; transform: translateY(10px); }
            to   { opacity: 1; transform: translateY(0); }
        }
        @keyframes softPop {
            from { opacity: 0; transform: translateY(8px); }
            to   { opacity: 1; transform: translateY(0); }
        }

        .anim-left { animation: fadeSlideIn .55s ease-out both; }
        .stagger-1 { animation: softPop .45s ease-out both; animation-delay: .10s; }
        .stagger-2 { animation: softPop .45s ease-out both; animation-delay: .18s; }
        .stagger-3 { animation: softPop .45s ease-out both; animation-delay: .26s; }

        @media (prefers-reduced-motion: reduce) {
            .anim-left, .stagger-1, .stagger-2, .stagger-3 { animation: none !important; }
        }
    </style>
</head>

<body class="min-h-screen flex flex-col md:flex-row bg-gray-50 md:bg-white">

    <!-- LEFT PANEL (UPDATED ONLY) -->
    <div class="w-full md:w-1/2 h-80 md:h-auto combo-gradient text-white relative
                shadow-xl md:shadow-none rounded-b-[3rem] md:rounded-none overflow-hidden anim-left">

        <div class="absolute inset-0 dot-texture opacity-60"></div>
        <div class="absolute inset-0 pitch-pattern"></div>

        <div class="relative z-10 p-8 md:p-12 h-full flex flex-col">

            <!-- Brand + badge -->
            <div class="flex items-start justify-between gap-3 stagger-1">
                <h1 class="text-4xl md:text-[50px] font-bold leading-none">
                    PadangPro<span class="text-blue-400 md:text-blue-500">.</span>
                </h1>

                <div class="hidden md:inline-flex items-center gap-2 text-xs bg-white/10 border border-white/15
                            px-3 py-1 rounded-full text-white/90 whitespace-nowrap">
                    <span>🏟️</span>
                    <span>Arena Owl Trafford, Bangi</span>
                </div>
            </div>

            <!-- Desktop supporting line -->
            <p class="mt-4 text-white/80 max-w-md leading-relaxed hidden md:block stagger-2">
                Create your account and start booking pitches, matching players, and organizing games — all in one place.
            </p>

            <!-- Desktop features (optional but nice) -->
            <div class="hidden md:grid grid-cols-1 gap-3 mt-8 max-w-md stagger-3">
                <div class="flex items-start gap-3 bg-white/10 border border-white/15 rounded-xl px-4 py-3">
                    <div class="text-lg">📅</div>
                    <div>
                        <p class="font-semibold leading-tight">Easy Booking</p>
                        <p class="text-white/75 text-sm">Find slots quickly and reserve in minutes.</p>
                    </div>
                </div>

                <div class="flex items-start gap-3 bg-white/10 border border-white/15 rounded-xl px-4 py-3">
                    <div class="text-lg">⚽</div>
                    <div>
                        <p class="font-semibold leading-tight">Player Matching</p>
                        <p class="text-white/75 text-sm">Connect with players based on roles and availability.</p>
                    </div>
                </div>
            </div>

            <!-- Desktop tagline -->
            <div class="hidden md:block mt-auto pb-6">
                <div class="font-bold italic text-[86px] leading-[0.9]">
                    Reserve.<br>
                    Play.<br>
                    Repeat.
                </div>
            </div>

            <!-- Mobile copy -->
            <div class="md:hidden flex flex-col justify-center h-full pb-10 text-center stagger-2">
                <p class="text-2xl font-bold italic opacity-90">Create your account</p>
                <p class="text-green-200 text-sm mt-2">Start booking pitches and matching with players.</p>
            </div>
        </div>
    </div>

    <!-- RIGHT PANEL (UNCHANGED) -->
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
