<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PadangPro | Arena Owl Trafford</title>
    @vite('resources/css/landing.css')
</head>
<body class="bg-gray-50 text-gray-800">

    <!-- Navbar -->
    <nav class="bg-green-700 text-white shadow-md fixed w-full z-50">
        <div class="max-w-7xl mx-auto px-6 py-4 flex justify-between items-center">
            <h1 class="text-2xl font-bold tracking-wide">PadangPro</h1>
            <div class="space-x-8">
                <a href="#about" class="hover:text-yellow-300">About</a>
                <a href="#services" class="hover:text-yellow-300">Our Services</a>
                <a href="{{ route('login') }}" class="bg-yellow-400 hover:bg-yellow-300 text-black px-4 py-2 rounded-lg font-semibold transition">Login</a>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="relative bg-cover bg-center h-[90vh] flex items-center justify-center"
        style="background-image: url('https://images.unsplash.com/photo-1508264165352-258859e62245?auto=format&fit=crop&w=1400&q=80');">
        <div class="absolute inset-0 bg-black bg-opacity-60"></div>
        <div class="relative text-center text-white px-6">
            <h2 class="text-5xl md:text-6xl font-extrabold mb-6">Welcome to Arena Owl Trafford</h2>
            <p class="text-lg md:text-xl mb-8 max-w-2xl mx-auto">
                The best football pitch in Bangi, Selangor — powered by PadangPro. Book your slot, rent equipment, and find your next match all in one place.
            </p>
            <a href="{{ route('login') }}" class="bg-yellow-400 hover:bg-yellow-300 text-black px-6 py-3 rounded-lg font-semibold text-lg transition">Get Started</a>
        </div>
    </section>

    <!-- About Section -->
    <section id="about" class="py-20 bg-white">
        <div class="max-w-6xl mx-auto px-6 text-center">
            <h3 class="text-3xl font-bold mb-6 text-green-700">About Arena Owl Trafford</h3>
            <p class="text-gray-700 leading-relaxed max-w-3xl mx-auto">
                Arena Owl Trafford is a premier football facility located in the heart of Bangi, Selangor. Designed for both casual and competitive players, 
                our pitch offers top-quality turf, modern amenities, and a seamless digital booking experience through PadangPro.
            </p>
        </div>
    </section>

    <!-- Services Section -->
    <section id="services" class="py-20 bg-green-700 text-white">
        <div class="max-w-6xl mx-auto px-6 text-center">
            <h3 class="text-3xl font-bold mb-10">Our Services</h3>

            <div class="grid md:grid-cols-3 gap-10">
                <div class="bg-green-600 p-8 rounded-xl shadow-lg hover:scale-105 transition">
                    <h4 class="text-xl font-semibold mb-3">Online Pitch Booking</h4>
                    <p>Book your favorite slot at Arena Owl Trafford with just a few clicks through our online system.</p>
                </div>

                <div class="bg-green-600 p-8 rounded-xl shadow-lg hover:scale-105 transition">
                    <h4 class="text-xl font-semibold mb-3">Equipment Rental</h4>
                    <p>Need jerseys, cones, or footballs? Rent everything you need easily from our rental system.</p>
                </div>

                <div class="bg-green-600 p-8 rounded-xl shadow-lg hover:scale-105 transition">
                    <h4 class="text-xl font-semibold mb-3">Matchmaking</h4>
                    <p>Looking for a team to play with or against? Find opponents and teammates through our matchmaking feature.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-gray-900 text-gray-400 text-center py-6 mt-10">
        <p>&copy; {{ date('Y') }} PadangPro | Arena Owl Trafford. All rights reserved.</p>
    </footer>

</body>
</html>
