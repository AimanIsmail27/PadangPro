<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Customer Dashboard - PadangPro</title>
    @vite('resources/css/app.css')
    <style>
        body {
            margin: 0;
            font-family: 'Poppins', sans-serif;
            background-color: #f8f9fa;
            display: flex;
        }

        /* Sidebar */
        aside {
            width: 220px;
            background: white;
            color: #000;
            height: 100vh;
            display: flex;
            flex-direction: column;
            padding: 20px;
            position: fixed;
            top: 0;
            left: 0;
        }

        aside .logo {
            font-size: 1.5rem;
            font-weight: bold;
            margin-bottom: 40px;
        }

        aside nav ul {
            list-style: none;
            padding: 0;
        }

        aside nav ul li {
            padding: 12px 0;
            cursor: pointer;
            font-weight: 500;
        }

        aside nav ul li:hover {
            background: rgba(255, 255, 255, 0.3);
            border-radius: 8px;
            padding-left: 10px;
            transition: 0.3s;
        }

        /* Main Content */
        main {
            margin-left: 220px; /* slightly increased to reduce empty gap */
            flex: 1;
            padding: 20px 30px; /* reduced horizontal padding */
        }

        /* Blue Welcome Banner */
        .welcome-banner {
            background-color: #1E2A78;
            color: white;
            padding: 85px 30px;
            border-radius: 10px;
            font-size: 1.6rem;
            font-weight: bold;
            margin-bottom: 25px;
            width: 100%; /* Make it match Recent Activity container */
            box-sizing: border-box;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
        }

        /* Dashboard Cards */
        .dashboard-cards {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 20px;
            margin-bottom: 30px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
        }

        .card {
            background: white;
            border-radius: 12px;
            padding: 20px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.2);
            text-align: center;
        }

        .card h2 {
            margin: 0;
            font-size: 1.4rem;
            color: #333;
        }

        .card p {
            font-size: 1.1rem;
            color: #666;
        }

        /* Recent Activity */
        .recent-activity {
            background: white;
            padding: 20px;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.2);
            width: 100%; /* Ensure same width as welcome banner */
            box-sizing: border-box;
        }

        .recent-activity h2 {
            margin: 0 0 20px;
        }

        .recent-activity ul {
            list-style: none;
            padding: 0;
        }

        .recent-activity ul li {
            padding: 10px 0;
            border-bottom: 1px solid #eee;
        }

        .recent-activity ul li:last-child {
            border-bottom: none;
        }

        .nav-item.dropdown .dropdown-menu {
    display: none;   /* hide initially */
    padding-left: 15px;  /* optional indent */
}

/* Show submenu on hover */
.nav-item.dropdown:hover .dropdown-menu {
    display: block;
}

/* Optional: smooth fade-in */
.nav-item.dropdown .dropdown-menu {
    transition: all 0.3s ease;
}
    </style>
</head>
<body>
    <!-- Sidebar -->
    <aside>
        <div class="logo">PadangPro</div>
        <nav>
            <ul>
                <li>Dashboard</li>
                <li><a href="{{ route('customer.profile') }}">Profile</a></li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown">Booking</a>
                    <ul class="dropdown-menu">
                    <li><a class="dropdown-item" href="{{ route('booking.view') }}">Your Booking History</a></li>
                    <li><a class="dropdown-item" href="{{ route('booking.page', 'F01') }}">Book Standard Field</a></li>
                    <li><a class="dropdown-item" href="{{ route('booking.mini') }}">Book Mini Pitch</a></li>
            </ul>
            </li>
                                   

                <li>Rental</li>
                <li>Matchmaking</li>
                <li>Rating and Review</li>
                <li><a href="{{ route('logout') }}">Logout</a></li>
            </ul>
        </nav>
    </aside>

    <!-- Main Content -->
    <main>
        <!-- Blue Welcome Banner -->
        <div class="welcome-banner">
            WELCOME, {{ Auth::user()->name ?? 'Customer' }}
        </div>

        <!-- Dashboard Cards -->
        <section class="dashboard-cards">
            <div class="card">
                <h2>Total Bookings</h2>
                <p>12</p>
            </div>
            <div class="card">
                <h2>Upcoming Matches</h2>
                <p>3</p>
            </div>
            <div class="card">
                <h2>Profile Completion</h2>
                <p>80%</p>
            </div>
        </section>

        <!-- Recent Activity -->
        <section class="recent-activity">
            <h2>Recent Activity</h2>
            <ul>
                <li>Booked: Stadium ABC - 28 Aug 2025</li>
                <li>Paid Deposit: Pitch XYZ</li>
                <li>Joined Team: Tigers FC</li>
            </ul>
        </section>
    </main>

    @if(session('payment_success'))
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        Swal.fire({
            title: 'Booking Confirmed!',
            text: "{{ session('payment_success') }}",
            icon: 'success',
            confirmButtonText: 'Okay'
        });
    </script>
@endif

</body>
</html>
