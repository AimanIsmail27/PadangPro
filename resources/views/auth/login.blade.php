<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - PadangPro</title>

    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate" />
    <meta http-equiv="Pragma" content="no-cache" />
    <meta http-equiv="Expires" content="0" />

    <style>
        /* ✅ Original gradient (unchanged) */
        .combo-gradient {
            background-image:
                linear-gradient(to bottom right, #15803d, #064e3b),
                linear-gradient(to right, rgba(21, 128, 61, 0.35), rgba(6, 78, 59, 0.55));
            background-blend-mode: overlay;
        }

        /* ✅ Dotted texture overlay */
        .dot-texture {
            background-image: radial-gradient(rgba(255,255,255,0.10) 1px, transparent 1px);
            background-size: 18px 18px;
        }

        /* ✅ Football pitch line pattern (very subtle, non-AI) */
        .pitch-pattern {
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='520' height='520' viewBox='0 0 520 520'%3E%3Cg fill='none' stroke='rgba(255,255,255,0.16)' stroke-width='2'%3E%3Crect x='26' y='26' width='468' height='468' rx='18'/%3E%3Cline x1='260' y1='26' x2='260' y2='494'/%3E%3Ccircle cx='260' cy='260' r='64'/%3E%3Ccircle cx='260' cy='260' r='6' fill='rgba(255,255,255,0.16)' stroke='none'/%3E%3Crect x='26' y='146' width='96' height='228' rx='10'/%3E%3Crect x='398' y='146' width='96' height='228' rx='10'/%3E%3Crect x='26' y='196' width='46' height='128' rx='10'/%3E%3Crect x='448' y='196' width='46' height='128' rx='10'/%3E%3C/g%3E%3C/svg%3E");
            background-size: 520px 520px;
            background-repeat: no-repeat;
            background-position: 85% 65%;
            opacity: .35;
        }

        /* Better focus */
        .input-focus:focus {
            outline: none;
            border-color: #60a5fa;
            box-shadow: 0 0 0 3px rgba(96,165,250,.25);
        }

        /* =========================
           ✅ Page animations (subtle)
           ========================= */

        /* Left panel fade + slight slide */
        @keyframes fadeSlideIn {
            from { opacity: 0; transform: translateY(10px); }
            to   { opacity: 1; transform: translateY(0); }
        }

        /* Card slide up + fade */
        @keyframes cardUp {
            from { opacity: 0; transform: translateY(18px); }
            to   { opacity: 1; transform: translateY(0); }
        }

        /* Small stagger for elements inside left panel */
        @keyframes softPop {
            from { opacity: 0; transform: translateY(8px); }
            to   { opacity: 1; transform: translateY(0); }
        }

        .anim-left {
            animation: fadeSlideIn .55s ease-out both;
        }

        .anim-card {
            animation: cardUp .55s ease-out both;
            animation-delay: .08s;
        }

        .stagger-1 { animation: softPop .45s ease-out both; animation-delay: .10s; }
        .stagger-2 { animation: softPop .45s ease-out both; animation-delay: .18s; }
        .stagger-3 { animation: softPop .45s ease-out both; animation-delay: .26s; }
        .stagger-4 { animation: softPop .45s ease-out both; animation-delay: .34s; }

        /* Respect users who prefer reduced motion */
        @media (prefers-reduced-motion: reduce) {
            .anim-left, .anim-card, .stagger-1, .stagger-2, .stagger-3, .stagger-4 {
                animation: none !important;
            }
        }
    </style>
</head>

<body class="min-h-screen flex flex-col md:flex-row bg-gray-50 md:bg-white">

    <!-- LEFT PANEL -->
    <div class="w-full md:w-1/2 h-80 md:h-auto combo-gradient text-white relative
                shadow-xl md:shadow-none rounded-b-[3rem] md:rounded-none overflow-hidden anim-left">

        <!-- overlays -->
        <div class="absolute inset-0 dot-texture opacity-60"></div>
        <div class="absolute inset-0 pitch-pattern"></div>

        <div class="relative z-10 p-8 md:p-12 h-full flex flex-col">

            <!-- Brand -->
            <div class="flex items-start justify-between stagger-1">
                <h1 class="text-4xl md:text-[50px] font-bold">
                    PadangPro<span class="text-blue-400">.</span>
                </h1>

                <!-- small badge (desktop only) -->
                <div class="hidden md:inline-flex items-center gap-2 text-xs bg-white/10 border border-white/15
                            px-3 py-1 rounded-full text-white/90">
                    <span>🏟️</span>
                    <span>Arena Owl Trafford, Bangi</span>
                </div>
            </div>

            <!-- Supporting line -->
            <p class="mt-4 text-white/80 max-w-md leading-relaxed hidden md:block stagger-2">
                A simple booking experience that helps you lock your slot fast, show up on time, and play more.
            </p>

            <!-- Desktop feature list -->
            <div class="hidden md:grid grid-cols-1 gap-3 mt-8 max-w-md stagger-3">
                <div class="flex items-start gap-3 bg-white/10 border border-white/15 rounded-xl px-4 py-3">
                    <div class="text-lg">⚡</div>
                    <div>
                        <p class="font-semibold leading-tight">Fast Booking</p>
                        <p class="text-white/75 text-sm">Reserve in a few clicks with clear slot availability.</p>
                    </div>
                </div>

                <div class="flex items-start gap-3 bg-white/10 border border-white/15 rounded-xl px-4 py-3">
                    <div class="text-lg">🕒</div>
                    <div>
                        <p class="font-semibold leading-tight">Real-Time Slots</p>
                        <p class="text-white/75 text-sm">Updated availability so you don’t double-book.</p>
                    </div>
                </div>

                <div class="flex items-start gap-3 bg-white/10 border border-white/15 rounded-xl px-4 py-3">
                    <div class="text-lg">🤝</div>
                    <div>
                        <p class="font-semibold leading-tight">Team & Match</p>
                        <p class="text-white/75 text-sm">Organize games and keep everyone on the same page.</p>
                    </div>
                </div>
            </div>

            <!-- Desktop tagline -->
            <div class="hidden md:block mt-auto pb-6 stagger-4">
                <div class="font-bold italic text-[86px] leading-[0.9]">
                    Reserve.<br>
                    Play.<br>
                    Repeat.
                </div>
            </div>

            <!-- Mobile content -->
            <div class="md:hidden flex flex-col justify-center h-full text-center stagger-2">
                <p class="text-2xl font-bold italic opacity-90">Reserve. Play. Repeat.</p>
                <p class="text-green-200 text-sm mt-2">Your ultimate sports booking partner</p>
                <p class="text-green-200 text-sm">Arena Owl Trafford, Bangi</p>

                <div class="mt-5 flex flex-wrap justify-center gap-2 stagger-3">
                    <span class="px-3 py-1 rounded-full bg-white/10 border border-white/15 text-sm">⚡ Fast Booking</span>
                    <span class="px-3 py-1 rounded-full bg-white/10 border border-white/15 text-sm">🕒 Real-Time</span>
                    <span class="px-3 py-1 rounded-full bg-white/10 border border-white/15 text-sm">🤝 Team</span>
                </div>
            </div>

        </div>
    </div>

    <!-- RIGHT PANEL -->
    <div class="w-full md:w-1/2 flex justify-center items-start md:items-center
                -mt-24 md:mt-0 px-4 md:px-0 relative z-10">

        <div class="w-full max-w-md p-8 bg-white rounded-3xl shadow-xl border border-slate-100 anim-card">

            <h2 class="text-2xl md:text-3xl font-semibold mb-6 text-gray-800">
                Login to your account
            </h2>

            @if(session('error'))
                <div class="bg-red-50 border-l-4 border-red-500 text-red-700 p-3 rounded mb-6 text-sm">
                    {{ session('error') }}
                </div>
            @endif

            <form action="{{ route('login') }}" method="POST" class="space-y-5">
                @csrf

                <div>
                    <label class="block text-sm font-medium mb-1 text-gray-700">Email</label>
                    <input type="email" name="email" value="{{ old('email') }}"
                           class="w-full border border-gray-300 rounded-lg px-4 py-3 input-focus transition"
                           placeholder="Enter your email">
                    @error('email')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium mb-1 text-gray-700">Password</label>
                    <div class="relative">
                        <input type="password" name="password" id="passwordInput"
                               class="w-full border border-gray-300 rounded-lg px-4 py-3 pr-10 input-focus transition"
                               placeholder="Enter your password">
                        <span onclick="togglePassword()" class="absolute right-3 top-3.5 cursor-pointer text-gray-400 hover:text-gray-600">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
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

                <a href="{{ route('login.google') }}"
                   class="w-full border border-gray-300 bg-gray-50 text-gray-700 py-3 rounded-lg flex items-center justify-center space-x-3 hover:bg-gray-200 transition font-semibold shadow-sm">
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

    @if (session('success'))
    <script>
        Swal.fire({
            icon: 'success',
            title: 'Success!',
            text: @json(session('success')),
            confirmButtonColor: '#2563eb',
            timer: 2200,
            timerProgressBar: true
        });
    </script>
    @endif

</body>
</html>
