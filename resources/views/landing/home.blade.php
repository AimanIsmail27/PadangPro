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
        body { font-family: 'Poppins', sans-serif; scroll-behavior: smooth; }
        .review-text { white-space: normal; overflow-wrap: break-word; word-wrap: break-word; display: block; max-height: none; }

        .navbar-scrolled {
            background-color: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            box-shadow: 0 2px 10px rgba(0,0,0,0.10);
        }
        .nav-link.active { color: #facc15; font-weight: 600; }

        /* Premium helpers */
        .section-kicker { letter-spacing: .14em; text-transform: uppercase; font-weight: 700; font-size: .75rem; color: #f59e0b; }
        .section-title { font-size: clamp(1.75rem, 3vw, 2.6rem); line-height: 1.1; }
        .section-subtitle { color: #6b7280; max-width: 52rem; }

        .soft-shadow { box-shadow: 0 18px 60px rgba(17, 24, 39, 0.10); }
        .glass { background: rgba(255,255,255,0.72); backdrop-filter: blur(10px); border: 1px solid rgba(255,255,255,0.65); }
        .blob { filter: blur(60px); opacity: .45; transform: translateZ(0); }

        .hover-lift { transition: transform .25s ease, box-shadow .25s ease; }
        .hover-lift:hover { transform: translateY(-6px); box-shadow: 0 22px 70px rgba(17, 24, 39, 0.14); }

        .btn-primary {
            background: #facc15;
            color: #111827;
            font-weight: 800;
            border-radius: 12px;
            padding: 0.85rem 1.2rem;
            transition: .25s ease;
            display: inline-flex;
            align-items: center;
            gap: .5rem;
        }
        .btn-primary:hover { background: #fde047; transform: translateY(-1px); }

        .btn-outline {
            border: 1px solid rgba(17,24,39,.15);
            background: rgba(255,255,255,.7);
            border-radius: 12px;
            padding: 0.85rem 1.2rem;
            font-weight: 800;
            transition: .25s ease;
        }
        .btn-outline:hover { background: #fff; transform: translateY(-1px); }

        /* Scroll progress */
        #scrollProgress {
            position: fixed;
            top: 0;
            left: 0;
            height: 3px;
            width: 0%;
            background: linear-gradient(90deg, #facc15, #fb923c);
            z-index: 60;
        }
    </style>
</head>

<body class="bg-gray-50 text-gray-800">
    <div id="scrollProgress"></div>

    {{-- NAVBAR --}}
    <header id="navbar" class="fixed top-0 left-0 w-full z-50 bg-white/90 backdrop-blur-md shadow-md transition-all duration-300">
        <nav class="container mx-auto flex items-center justify-between px-6 py-4">
            <a href="#home" class="text-xl font-bold text-yellow-500 nav-link">PadangPro</a>


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

    {{-- HERO --}}
    <section id="home" class="relative bg-cover bg-center h-screen"

             style="background-image: url('{{ asset('images/padangcoverpage.jpg') }}');">
        <div class="absolute inset-0 bg-black bg-opacity-60 flex items-center justify-center">
            <div class="text-center text-white px-4" data-aos="fade-up">
                <h1 class="text-5xl font-bold mb-4">Welcome to PadangPro</h1>
                <p class="text-lg mb-6">
                    Book your football matches easily at
                    <span class="font-semibold">Arena Owl Trafford</span>, Bangi, Selangor.
                </p>
                <a href="{{ route('login') }}" class="btn-primary">
                    Login to Get Started <span>→</span>
                </a>
            </div>
        </div>
    </section>

    {{-- ABOUT (UPGRADED) --}}
<section class="relative py-20 bg-gradient-to-b from-amber-50 to-yellow-100 overflow-hidden" id="about">
        <div class="absolute -top-24 -left-24 w-72 h-72 rounded-full bg-yellow-300 blob"></div>
        <div class="absolute -bottom-24 -right-24 w-72 h-72 rounded-full bg-orange-200 blob"></div>

        <div class="container mx-auto px-6 md:px-12 relative">
            <div class="grid lg:grid-cols-2 gap-10 items-center">
                <div data-aos="fade-right">
                    <p class="section-kicker">About the venue</p>
                    <h2 class="section-title font-bold text-gray-900 mt-2">
                        Arena Owl Trafford — where games feel like tournaments.
                    </h2>
                    <p class="section-subtitle mt-4 leading-relaxed">
                        Located in the heart of Bangi, Selangor — Arena Owl Trafford offers a professional-grade football pitch that’s perfect
                        for casual games, team practices, or tournaments. Managed by PadangPro, we make the entire booking process seamless
                        and digital — so you can focus on the game.
                    </p>

                    <div class="mt-8 grid sm:grid-cols-3 gap-4">
                        <div class="glass rounded-2xl p-4 soft-shadow hover-lift" data-aos="zoom-in" data-aos-delay="50">
                            <p class="text-2xl font-bold text-gray-900">24/7</p>
                            <p class="text-sm text-gray-600 mt-1">Open daily</p>
                        </div>
                        <div class="glass rounded-2xl p-4 soft-shadow hover-lift" data-aos="zoom-in" data-aos-delay="120">
                            <p class="text-2xl font-bold text-gray-900">2H</p>
                            <p class="text-sm text-gray-600 mt-1">Per slot</p>
                        </div>
                        <div class="glass rounded-2xl p-4 soft-shadow hover-lift" data-aos="zoom-in" data-aos-delay="190">
                            <p class="text-2xl font-bold text-gray-900">Fast</p>
                            <p class="text-sm text-gray-600 mt-1">Online booking</p>
                        </div>
                    </div>

                    <div class="mt-8 flex flex-wrap gap-3">
                        <a href="#services" class="btn-primary" data-aos="fade-up" data-aos-delay="220">
                            Explore Services <span>→</span>
                        </a>
                        <a href="#location" class="btn-outline" data-aos="fade-up" data-aos-delay="280">
                            Find Us
                        </a>
                    </div>
                </div>

                <div class="relative" data-aos="fade-left">
                    <div class="rounded-3xl overflow-hidden soft-shadow">
                        <img src="{{ asset('images/padangcoverpage.jpg') }}" alt="Arena Owl Trafford" class="w-full h-[360px] object-cover">
                    </div>
                    <div class="absolute -bottom-6 left-6 glass rounded-2xl p-4 soft-shadow w-[88%] md:w-[70%]" data-aos="fade-up" data-aos-delay="150">
                        <p class="font-bold text-gray-900">PadangPro Booking Experience</p>
                        <p class="text-sm text-gray-600 mt-1">
                            Transparent pricing, instant availability, and secure payment — all in one place.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- SERVICES (UPGRADED) --}}
<section class="relative py-20 bg-gradient-to-b from-amber-100 to-slate-100 overflow-hidden" id="services">
        <div class="absolute top-10 right-[-80px] w-72 h-72 rounded-full bg-yellow-200 blob"></div>
        <div class="absolute bottom-0 left-[-80px] w-72 h-72 rounded-full bg-orange-100 blob"></div>

        <div class="container mx-auto px-6 md:px-12 relative">
            <div class="text-center max-w-3xl mx-auto" data-aos="fade-up">
                <p class="section-kicker">What we offer</p>
                <h2 class="section-title font-bold text-gray-900 mt-2">Services built for players</h2>
                <p class="section-subtitle mt-4">
                    Everything you need from booking to matchday — with simple workflows and a clean interface.
                </p>
            </div>

            <div class="mt-12 grid sm:grid-cols-2 lg:grid-cols-4 gap-6">
                <div class="glass rounded-3xl p-7 soft-shadow hover-lift" data-aos="zoom-in">
                    <div class="w-12 h-12 rounded-2xl bg-yellow-100 flex items-center justify-center text-2xl">⚽</div>
                    <h3 class="text-lg font-bold text-gray-900 mt-5">Pitch Booking</h3>
                    <p class="text-gray-600 mt-2 text-sm leading-relaxed">
                        View availability, select a time, and confirm your slot in minutes.
                    </p>
                    <div class="mt-5 text-sm font-semibold text-yellow-600">Learn more →</div>
                </div>

                <div class="glass rounded-3xl p-7 soft-shadow hover-lift" data-aos="zoom-in" data-aos-delay="80">
                    <div class="w-12 h-12 rounded-2xl bg-yellow-100 flex items-center justify-center text-2xl">🎽</div>
                    <h3 class="text-lg font-bold text-gray-900 mt-5">Item Rental</h3>
                    <p class="text-gray-600 mt-2 text-sm leading-relaxed">
                        Rent jerseys, cones, balls, and more to complete your setup.
                    </p>
                    <div class="mt-5 text-sm font-semibold text-yellow-600">Learn more →</div>
                </div>

                <div class="glass rounded-3xl p-7 soft-shadow hover-lift" data-aos="zoom-in" data-aos-delay="160">
                    <div class="w-12 h-12 rounded-2xl bg-yellow-100 flex items-center justify-center text-2xl">🤝</div>
                    <h3 class="text-lg font-bold text-gray-900 mt-5">Matchmaking</h3>
                    <p class="text-gray-600 mt-2 text-sm leading-relaxed">
                        Find opponents or join teams looking for players based on your schedule.
                    </p>
                    <div class="mt-5 text-sm font-semibold text-yellow-600">Learn more →</div>
                </div>

                <div class="glass rounded-3xl p-7 soft-shadow hover-lift" data-aos="zoom-in" data-aos-delay="240">
                    <div class="w-12 h-12 rounded-2xl bg-yellow-100 flex items-center justify-center text-2xl">💳</div>
                    <h3 class="text-lg font-bold text-gray-900 mt-5">Online Payment</h3>
                    <p class="text-gray-600 mt-2 text-sm leading-relaxed">
                        Pay securely with a smooth confirmation flow and clear receipts.
                    </p>
                    <div class="mt-5 text-sm font-semibold text-yellow-600">Learn more →</div>
                </div>
            </div>
        </div>
    </section>

    {{-- LOCATION (UPGRADED) --}}
<section class="relative py-20 bg-gradient-to-b from-slate-100 to-white overflow-hidden" id="location">
        <div class="absolute -top-24 right-[-120px] w-80 h-80 rounded-full bg-yellow-200 blob"></div>
        <div class="absolute -bottom-24 left-[-120px] w-80 h-80 rounded-full bg-orange-100 blob"></div>

        <div class="container mx-auto px-6 md:px-12 relative">
            <div class="text-center mb-12" data-aos="fade-up">
                <p class="section-kicker">Location</p>
                <h2 class="section-title font-bold text-gray-900 mt-2">Find Us</h2>
                <p class="section-subtitle mx-auto mt-4 leading-relaxed">
                    We're located in Bangi, Selangor. Come visit us for your next game!
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-8 items-center">
                <div class="w-full h-96 rounded-3xl soft-shadow overflow-hidden" data-aos="fade-right">
                    <iframe
                        src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3984.62939599811!2d101.7453147758362!3d2.979425054366668!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x31cdb9f345388a1b%3A0x8383e74c69ebf52b!2sArena%20Owl%20Trafford!5e0!3m2!1sen!2smy"
                        width="100%" height="100%" style="border:0;" allowfullscreen="" loading="lazy"
                        referrerpolicy="no-referrer-when-downgrade">
                    </iframe>
                </div>

                <div data-aos="fade-left">
                    <div class="glass rounded-3xl p-8 soft-shadow border border-white/60">
                        <h3 class="text-2xl font-bold text-gray-900 mb-6">Arena Owl Trafford</h3>

                        <div class="space-y-5 text-gray-700">
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

                        <a href="https://www.google.com/maps/dir/?api=1&destination=Arena+Owl+Trafford&destination_place_id=ChIJFVG7NSzLzTERiUbuqJoUAhQ"
                           target="_blank"
                           class="btn-primary mt-7">
                           Get Directions <span>↗</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- REVIEWS (UPGRADED) --}}
    <section class="relative py-20 bg-gray-50 overflow-hidden" id="reviews">
        <div class="absolute -top-24 -left-24 w-80 h-80 rounded-full bg-yellow-200 blob"></div>
        <div class="absolute -bottom-24 -right-24 w-80 h-80 rounded-full bg-orange-100 blob"></div>

        <div class="container mx-auto px-6 md:px-12 text-center relative">
            <div data-aos="fade-up">
                <p class="section-kicker">Community</p>
                <h2 class="section-title font-bold text-gray-900 mt-2">What Players Say</h2>
                <p class="section-subtitle mx-auto mt-4">
                    Real feedback from players — refreshed with the latest reviews.
                </p>
            </div>

            <div id="reviews-container" class="mt-12 grid md:grid-cols-3 gap-6 justify-center">
                <p class="text-gray-500">Loading latest reviews...</p>
            </div>
        </div>
    </section>

    {{-- FOOTER --}}
    <footer class="bg-gray-900 text-gray-300 py-8 text-center">
        <p>© {{ date('Y') }} PadangPro. All Rights Reserved.</p>
    </footer>

    <script src="https://unpkg.com/aos@2.3.4/dist/aos.js"></script>
    <script>
        AOS.init({ duration: 1000, once: true });

        const navbar = document.getElementById('navbar');
        const progressEl = document.getElementById('scrollProgress');

        let ticking = false;

        function handleScroll() {
            const y = window.scrollY || document.documentElement.scrollTop;

            // Navbar style toggle
            if (y > 80) navbar.classList.add('navbar-scrolled');
            else navbar.classList.remove('navbar-scrolled');

            // Progress bar
            const doc = document.documentElement;
            const docHeight = doc.scrollHeight - doc.clientHeight;
            const progress = docHeight ? (y / docHeight) * 100 : 0;
            progressEl.style.width = progress + '%';

            ticking = false;
        }

        window.addEventListener('scroll', () => {
            if (!ticking) {
                window.requestAnimationFrame(handleScroll);
                ticking = true;
            }
        }, { passive: true });

        // run once on load so navbar/progress is correct if page not at top
        handleScroll();


        // Smooth scroll & active link handling
        document.querySelectorAll('.nav-link').forEach(link => {
            link.addEventListener('click', function(e) {
                e.preventDefault();
                document.querySelectorAll('.nav-link').forEach(l => l.classList.remove('active'));
                this.classList.add('active');

                const targetId = this.getAttribute('href').substring(1);
                const target = document.getElementById(targetId);
                if (target) {
                    window.scrollTo({
                        top: target.offsetTop - 80,
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

                if (!reviews || reviews.length === 0) {
                    container.innerHTML = '<p class="text-gray-500">No reviews yet.</p>';
                    return;
                }

                reviews.forEach((review, index) => {
                    let stars = '';
                    for (let i = 1; i <= 5; i++) stars += i <= review.rating_Score ? '★' : '☆';

                    const card = document.createElement('div');
                    card.className = "glass rounded-3xl p-7 soft-shadow hover-lift text-left";
                    card.setAttribute("data-aos", "fade-up");
                    card.setAttribute("data-aos-delay", index * 100);

                    card.innerHTML = `
                        <p class="text-yellow-500 text-2xl mb-3">${stars}</p>
                        <p class="text-gray-700 mb-4 italic review-text">"${review.review_Given}"</p>
                        <h4 class="font-bold text-gray-900">– ${review.customer_FullName ?? 'Anonymous Player'}</h4>
                        <p class="text-gray-400 text-xs mt-2">
                            ${new Date(review.review_Date).toLocaleDateString('en-US', { year: 'numeric', month: 'long', day: 'numeric' })}
                        </p>
                    `;
                    container.appendChild(card);
                });

                AOS.refresh();
            } catch (error) {
                console.error('Error fetching reviews:', error);
                document.getElementById('reviews-container').innerHTML =
                    '<p class="text-red-500">Could not load reviews.</p>';
            }
        }

        document.addEventListener('DOMContentLoaded', loadReviews);
    </script>
</body>
</html>
