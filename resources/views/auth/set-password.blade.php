<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Set Password - PadangPro</title>

    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate" />
    <meta http-equiv="Pragma" content="no-cache" />
    <meta http-equiv="Expires" content="0" />

    <style>
        /* ✅ Same as updated login pages */
        .combo-gradient {
            background-image:
                linear-gradient(to bottom right, #15803d, #064e3b),
                linear-gradient(to right, rgba(21, 128, 61, 0.35), rgba(6, 78, 59, 0.55));
            background-blend-mode: overlay;
        }

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

        .input-focus:focus {
            outline: none;
            border-color: #60a5fa;
            box-shadow: 0 0 0 3px rgba(96,165,250,.25);
        }

        /* ✅ Animations (same behaviour) */
        @keyframes fadeSlideIn {
            from { opacity: 0; transform: translateY(10px); }
            to   { opacity: 1; transform: translateY(0); }
        }
        @keyframes cardUp {
            from { opacity: 0; transform: translateY(18px); }
            to   { opacity: 1; transform: translateY(0); }
        }
        @keyframes softPop {
            from { opacity: 0; transform: translateY(8px); }
            to   { opacity: 1; transform: translateY(0); }
        }

        .anim-left { animation: fadeSlideIn .55s ease-out both; }
        .anim-card { animation: cardUp .55s ease-out both; animation-delay: .08s; }

        .stagger-1 { animation: softPop .45s ease-out both; animation-delay: .10s; }
        .stagger-2 { animation: softPop .45s ease-out both; animation-delay: .18s; }
        .stagger-3 { animation: softPop .45s ease-out both; animation-delay: .26s; }

        @media (prefers-reduced-motion: reduce) {
            .anim-left, .anim-card, .stagger-1, .stagger-2, .stagger-3 { animation: none !important; }
        }
    </style>
</head>

<body class="min-h-screen flex flex-col md:flex-row bg-gray-50 md:bg-white">

    <!-- LEFT PANEL -->
    <div class="w-full md:w-1/2 h-80 md:h-auto combo-gradient text-white relative
                shadow-xl md:shadow-none rounded-b-[3rem] md:rounded-none overflow-hidden anim-left">

        <div class="absolute inset-0 dot-texture opacity-60"></div>
        <div class="absolute inset-0 pitch-pattern"></div>

        <div class="relative z-10 p-8 md:p-12 h-full flex flex-col">

            <div class="flex items-start justify-between stagger-1">
                <h1 class="text-4xl md:text-[50px] font-bold">
                    PadangPro<span class="text-blue-400">.</span>
                </h1>

                <div class="hidden md:inline-flex items-center gap-2 text-xs bg-white/10 border border-white/15
                            px-3 py-1 rounded-full text-white/90">
                    <span>🏟️</span>
                    <span>Arena Owl Trafford, Bangi</span>
                </div>
            </div>

            <!-- Desktop message -->
            <p class="mt-4 text-white/80 max-w-md leading-relaxed hidden md:block stagger-2">
                One last step — set a password to enable email & password login alongside Google login.
            </p>

            <!-- Desktop tagline -->
            <div class="hidden md:block mt-auto pb-6 stagger-3">
                <div class="font-bold italic text-[86px] leading-[0.9]">
                    Reserve.<br>
                    Play.<br>
                    Repeat.
                </div>
            </div>

            <!-- Mobile content -->
            <div class="md:hidden flex flex-col justify-center h-full text-center stagger-2">
                <p class="text-2xl font-bold italic opacity-90">Set Your Password</p>
                <p class="text-green-200 text-sm mt-2">One last step before accessing your dashboard.</p>
            </div>

        </div>
    </div>

    <!-- RIGHT PANEL -->
    <div class="w-full md:w-1/2 flex justify-center items-start md:items-center
                -mt-24 md:mt-0 px-4 md:px-0 relative z-10">

        <div class="w-full max-w-md p-8 bg-white rounded-3xl shadow-xl border border-slate-100 anim-card">

            <h2 class="text-2xl md:text-3xl font-semibold mb-2 text-gray-800">Set Password</h2>
            <p class="text-gray-600 mb-6 text-sm">Create a password to enable email & password login.</p>

            @if(session('info'))
                <div class="bg-blue-50 border-l-4 border-blue-500 text-blue-700 p-3 rounded mb-6 text-sm">
                    {{ session('info') }}
                </div>
            @endif

            @if($errors->any())
                <div class="bg-red-50 border-l-4 border-red-500 text-red-700 p-3 rounded mb-6 text-sm">
                    <ul class="list-disc pl-5">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('google.password.save') }}" method="POST" class="space-y-5">
                @csrf

                <!-- New password -->
                <div>
                    <label class="block text-sm font-medium mb-1 text-gray-700">New Password</label>

                    <div class="relative">
                        <input type="password" name="password" id="setPassword" required minlength="8"
                               class="w-full border border-gray-300 rounded-lg px-4 py-3 pr-10 input-focus transition"
                               placeholder="At least 8 characters">

                        <span onclick="toggleField('setPassword')"
                              class="absolute right-3 top-3.5 cursor-pointer text-gray-400 hover:text-gray-600">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                 stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                      d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z" />
                                <path stroke-linecap="round" stroke-linejoin="round"
                                      d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                        </span>
                    </div>

                    <p class="text-xs text-gray-500 mt-2">Tip: Use 8+ characters with numbers or symbols.</p>
                </div>

                <!-- Confirm password -->
                <div>
                    <label class="block text-sm font-medium mb-1 text-gray-700">Confirm Password</label>

                    <div class="relative">
                        <input type="password" name="password_confirmation" id="confirmSetPassword" required minlength="8"
                               class="w-full border border-gray-300 rounded-lg px-4 py-3 pr-10 input-focus transition"
                               placeholder="Repeat your password">

                        <span onclick="toggleField('confirmSetPassword')"
                              class="absolute right-3 top-3.5 cursor-pointer text-gray-400 hover:text-gray-600">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                 stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                      d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z" />
                                <path stroke-linecap="round" stroke-linejoin="round"
                                      d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                        </span>
                    </div>
                </div>

                <button type="submit"
                        class="w-full bg-blue-600 text-white py-3 rounded-lg hover:bg-blue-700 transition font-semibold text-lg shadow-md">
                    Save Password
                </button>
            </form>
        </div>

        <div class="h-10 md:hidden"></div>
    </div>

    <script>
        function toggleField(id) {
            const el = document.getElementById(id);
            el.type = (el.type === "password") ? "text" : "password";
        }
    </script>

</body>
</html>
