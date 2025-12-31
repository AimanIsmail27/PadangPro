<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PadangPro - Arena Owl Trafford</title>
    <link rel="icon" type="image/png" href="{{ asset('images/logoPadang.png') }}">
<link rel="shortcut icon" type="image/png" href="{{ asset('images/logoPadang.png') }}">
    <script src="https://cdn.tailwindcss.com"></script>

    <link href="https://unpkg.com/aos@2.3.4/dist/aos.css" rel="stylesheet">
    
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">

    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">

    <style>
        body {
            font-family: 'Poppins', sans-serif;
            scroll-behavior: smooth;
        }
        .review-text {
            white-space: normal;
            overflow-wrap: break-word;
            word-wrap: break-word;
            display: block;
            max-height: none;
        }
        .navbar-scrolled {
            background-color: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .nav-link.active {
            color: #facc15; /* yellow-400 */
            font-weight: 600;
        }
    </style>
</head>
<body class="bg-gray-50 text-gray-800">

    <header id="navbar" class="fixed top-0 left-0 w-full z-50 bg-white/95 backdrop-blur-md shadow-md transition-all duration-300">
        <nav class="container mx-auto flex items-center justify-between px-6 py-4">
            <a href="#" class="text-xl font-bold text-yellow-500">PadangPro</a>
            <div class="space-x-6 text-gray-700 font-medium hidden md:block">
                <a href="#about" class="hover:text-yellow-500 transition nav-link">About</a>
                <a href="#services" class="hover:text-yellow-500 transition nav-link">Our Services</a>
                <a href="#location" class="hover:text-yellow-500 transition nav-link">Location</a>
                <a href="#reviews" class="hover:text-yellow-500 transition nav-link">What Players Say</a>
                <a href="{{ route('login') }}" class="bg-yellow-400 px-4 py-2 rounded-lg text-black hover:bg-yellow-300 transition">
                    Login
                </a>
            </div>
        </nav>
    </header>

    <section class="relative bg-cover bg-center h-screen" 
             style="background-image: url('{{ asset('images/padangcoverpage.jpg') }}');">
        <div class="absolute inset-0 bg-black bg-opacity-60 flex items-center justify-center">
            <div class="text-center text-white px-4" data-aos="fade-up">
                <h1 class="text-5xl font-bold mb-4">Welcome to PadangPro</h1>
                <p class="text-lg mb-6">
                    Book your football matches easily at 
                    <span class="font-semibold">Arena Owl Trafford</span>, Bangi, Selangor.
                </p>
                <a href="{{ route('login') }}" 
                   class="bg-yellow-400 text-black px-6 py-3 rounded-lg font-semibold hover:bg-yellow-300 transition">
                    Login to Get Started
                </a>
            </div>
        </div>
    </section>

    <section class="py-16 bg-white" id="about">
        <div class="container mx-auto px-6 md:px-12 text-center" data-aos="fade-up">
            <h2 class="text-3xl font-bold text-gray-800 mb-6">About Arena Owl Trafford</h2>
            <p class="text-gray-600 max-w-3xl mx-auto leading-relaxed">
                Located in the heart of Bangi, Selangor — Arena Owl Trafford offers a professional-grade football pitch that’s perfect for casual games, team practices, or tournaments. 
                Managed by PadangPro, we make the entire booking process seamless and digital — so you can focus on the game.
            </p>
        </div>
    </section>

    <section class="py-16 bg-gray-100" id="services">
        <div class="container mx-auto px-6 md:px-12 text-center">
            <h2 class="text-3xl font-bold text-gray-800 mb-10" data-aos="fade-up">Our Services</h2>

            <div class="grid md:grid-cols-4 gap-8">
                <div class="bg-white rounded-lg shadow-md p-8 hover:shadow-xl transition transform hover:-translate-y-1" data-aos="zoom-in">
                    <div class="text-yellow-500 text-5xl mb-4">⚽</div>
                    <h3 class="text-xl font-semibold mb-2">Pitch Booking</h3>
                    <p class="text-gray-600">Reserve your slot online for matches, training sessions, or casual games with ease.</p>
                </div>

                <div class="bg-white rounded-lg shadow-md p-8 hover:shadow-xl transition transform hover:-translate-y-1" data-aos="zoom-in" data-aos-delay="100">
                    <div class="text-yellow-500 text-5xl mb-4">🎽</div>
                    <h3 class="text-xl font-semibold mb-2">Football Item Rental</h3>
                    <p class="text-gray-600">
                        Need extra gear? Rent jerseys, cones, or balls directly from our platform to complete your matchday setup.
                    </p>
                </div>

                <div class="bg-white rounded-lg shadow-md p-8 hover:shadow-xl transition transform hover:-translate-y-1" data-aos="zoom-in" data-aos-delay="200">
                    <div class="text-yellow-500 text-5xl mb-4">🤝</div>
                    <h3 class="text-xl font-semibold mb-2">Matchmaking</h3>
                    <p class="text-gray-600">Find opponents or join teams looking for players based on your skill level and schedule.</p>
                </div>

                <div class="bg-white rounded-lg shadow-md p-8 hover:shadow-xl transition transform hover:-translate-y-1" data-aos="zoom-in" data-aos-delay="300">
                    <div class="text-yellow-500 text-5xl mb-4">💳</div>
                    <h3 class="text-xl font-semibold mb-2">Online Payment</h3>
                    <p class="text-gray-600">Pay securely for your bookings or team inventory through our trusted payment gateway.</p>
                </div>
            </div>
        </div>
    </section>

    <section class="py-16 bg-white" id="location">
        <div class="container mx-auto px-6 md:px-12">
            <div class="text-center mb-10" data-aos="fade-up">
                <h2 class="text-3xl font-bold text-gray-800">Find Us</h2>
                <p class="text-gray-600 max-w-3xl mx-auto leading-relaxed mt-4">
                    We're located in Bangi, Selangor. Come visit us for your next game!
                </p>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8 items-center">
                <div class="w-full h-96 rounded-xl shadow-2xl overflow-hidden" data-aos="fade-right">
                    
                    {{-- =============================================== --}}
                    {{-- THIS IS THE CORRECTED IFRAME --}}
                    {{-- =============================================== --}}
                    <iframe 
                        src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3984.62939599811!2d101.7453147758362!3d2.979425054366668!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x31cdb9f345388a1b%3A0x8383e74c69ebf52b!2sArena%20Owl%20Trafford!5e0!3m2!1sen!2smy" 
                        width="100%" 
                        height="100%" 
                        style="border:0;" 
                        allowfullscreen="" 
                        loading="lazy" 
                        referrerpolicy="no-referrer-when-downgrade">
                    </iframe>
                    {{-- =============================================== --}}

                </div>
                
                <div data-aos="fade-left">
                    <div class="bg-gray-50 rounded-xl p-8 shadow-lg border">
                        <h3 class="text-2xl font-bold text-gray-800 mb-4">Arena Owl Trafford</h3>
                        <div class="space-y-4 text-gray-700">
                            <p class="flex items-start gap-3">
                                <i class="bi bi-geo-alt-fill text-yellow-500 text-xl pt-1"></i>
                                <span>
                                    <strong class="text-gray-900 block">Address</strong>
                                    Jalan P10/20, Taman Perindustrian Selaman, 43650 Bandar Baru Bangi, Selangor
                                </span>
                            </p>
                            <p class="flex items-center gap-3">
                                <i class="bi bi-clock-fill text-yellow-500 text-xl"></i>
                                <span>
                                    <strong class="text-gray-900 block">Hours</strong>
                                    Open 24 hours
                                </span>
                            </p>
                             <p class="flex items-center gap-3">
                                <i class="bi bi-telephone-fill text-yellow-500 text-xl"></i>
                                <span>
                                    <strong class="text-gray-900 block">Phone</strong>
                                    +60 14-774 8619
                                </span>
                            </p>
                        </div>

                        {{-- =============================================== --}}
                        {{-- THIS IS THE CORRECTED "GET DIRECTIONS" LINK --}}
                        {{-- =============================================== --}}
                        <a href="https://www.google.com/maps/dir/?api=1&destination=Arena+Owl+Trafford&destination_place_id=ChIJFVG7NSzLzTERiUbuqJoUAhQ"
                           target="_blank" 
                           class="inline-block bg-yellow-400 text-black px-6 py-3 rounded-lg font-semibold hover:bg-yellow-300 transition mt-6">
                           Get Directions
                        </a>
                        {{-- =============================================== --}}
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="py-16 bg-gray-100" id="reviews">
        <div class="container mx-auto px-6 md:px-12 text-center">
            <h2 class="text-3xl font-bold text-gray-800 mb-10" data-aos="fade-up">What Players Say</h2>

            <div id="reviews-container" class="grid md:grid-cols-3 gap-8 justify-center">
                <p class="text-gray-500">Loading latest reviews...</p>
            </div>
        </div>
    </section>

    <footer class="bg-gray-900 text-gray-300 py-6 text-center" data-aos="fade-up">
        <p>© {{ date('Y') }} PadangPro. All Rights Reserved.</p>
    </footer>

    <script src="https://unpkg.com/aos@2.3.4/dist/aos.js"></script>
    <script>
        AOS.init({ duration: 1000, once: true });

        // 🌟 Navbar shadow toggle when scrolling
        window.addEventListener('scroll', () => {
            const navbar = document.getElementById('navbar');
            if (window.scrollY > 80) {
                navbar.classList.add('navbar-scrolled');
            } else {
                navbar.classList.remove('navbar-scrolled');
            }
        });

        // 🌟 Smooth scroll & active link handling
        document.querySelectorAll('.nav-link').forEach(link => {
            link.addEventListener('click', function(e) {
                e.preventDefault();
                document.querySelectorAll('.nav-link').forEach(l => l.classList.remove('active'));
                this.classList.add('active');
                const targetId = this.getAttribute('href').substring(1);
                const target = document.getElementById(targetId);
                if (target) {
                    window.scrollTo({
                        top: target.offsetTop - 80, // Offset for fixed navbar
                        behavior: 'smooth'
                    });
                }
            });
        });
    </script>

    <script>
    async function loadReviews() {
        try {
            const response = await fetch('/latest-reviews');
            const reviews = await response.json();

            const container = document.getElementById('reviews-container');
            container.innerHTML = '';

            if (reviews.length === 0) {
                container.innerHTML = '<p class="text-gray-500">No reviews yet.</p>';
                return;
            }

            reviews.forEach((review, index) => {
                let stars = '';
                for (let i = 1; i <= 5; i++) {
                    stars += i <= review.rating_Score ? '★' : '☆';
                }

                const card = document.createElement('div');
                card.className = "bg-white rounded-xl p-8 shadow-md hover:shadow-lg transition transform hover:-translate-y-1 text-left"; // Changed bg-gray-100 to bg-white
                card.setAttribute("data-aos", "fade-up");
                card.setAttribute("data-aos-delay", index * 100);
                card.innerHTML = `
                    <p class="text-yellow-500 text-2xl mb-3">${stars}</p>
                    <p class="text-gray-700 mb-4 italic review-text">"${review.review_Given}"</p>
                    <h4 class="font-semibold text-gray-800">– ${review.customer_FullName ?? 'Anonymous Player'}</h4>
                    <p class="text-gray-400 text-xs mt-2">${new Date(review.review_Date).toLocaleDateString('en-US', { year: 'numeric', month: 'long', day: 'numeric' })}</p>
                `;
                container.appendChild(card);
            });

            AOS.refresh();

        } catch (error) {
            console.error('Error fetching reviews:', error);
            const container = document.getElementById('reviews-container');
            container.innerHTML = '<p class="text-red-500">Could not load reviews.</p>';
        }
    }

    document.addEventListener('DOMContentLoaded', () => {
        loadReviews();
    });
    </script>
</body>
</html>