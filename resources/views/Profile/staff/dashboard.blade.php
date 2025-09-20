<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Administrator Dashboard - PadangPro</title>
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
            background: linear-gradient(180deg, #e9dfc1ff, #e9dfc1ff);
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

        aside nav ul li a {
    text-decoration: none; /* Remove underline */
    color: inherit; /* Use the same color as the text */
    display: block; /* Make the whole li clickable */
    padding: 12px 0;
}

aside nav ul li a:focus,
aside nav ul li a:active,
aside nav ul li a:visited {
    background: none; /* Remove default blue background */
    color: inherit; /* Keep original color */
    outline: none; /* Remove outline */
}


        aside nav ul li:hover {
            background: rgba(255, 255, 255, 0.3);
            border-radius: 8px;
            padding-left: 10px;
            transition: 0.3s;
        }

        /* Main Content */
        main {
            margin-left: 220px;
            flex: 1;
            padding: 20px 30px;
        }

        /* Yellow Welcome Banner */
        .welcome-banner {
            background-color: #FFD700; /* Changed to yellow */
            color: black;
            padding: 85px 30px;
            border-radius: 10px;
            font-size: 1.6rem;
            font-weight: bold;
            margin-bottom: 25px;
            width: 100%;
            box-sizing: border-box;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
        }

        /* Dashboard Cards */
        .dashboard-cards {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 20px;
            margin-bottom: 30px;
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
            width: 100%;
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
    </style>
</head>
<body>
    <!-- Sidebar -->
    <aside>
        <div class="logo">PadangPro</div>
        <nav>
            <ul>
                <li><a href="{{ route('staff.dashboard') }}">Dashboard</li>
                <li><a href="{{ route('staff.profile') }}">Profile</li>
                <li>Booking</li>
                <li>Rental</li>
                <li>Rating and Review</li>
                <li>Report</li>
                <li><a href="{{ route('logout') }}">Logout</a></li>
            </ul>
        </nav>
    </aside>

    <!-- Main Content -->
    <main>
        <!-- Yellow Welcome Banner -->
        <div class="welcome-banner">
            WELCOME, {{ Auth::user()->name ?? 'Staff' }}
        </div>

        <!-- Dashboard Cards -->
        <section class="dashboard-cards">
            <div class="card">
                <h2>Total Users</h2>
                <p>256</p>
            </div>
            <div class="card">
                <h2>Active Bookings</h2>
                <p>42</p>
            </div>
            <div class="card">
                <h2>Revenue This Month</h2>
                <p>RM 12,500</p>
            </div>
        </section>

        <!-- Recent Activity -->
        <section class="recent-activity">
            <h2>Recent Admin Actions</h2>
            <ul>
                <li>Added new stadium: Stadium Melati</li>
                <li>Approved booking: Pitch Alpha - 29 Aug 2025</li>
                <li>Deactivated user: user123@gmail.com</li>
            </ul>
        </section>
    </main>
</body>
</html>
