@extends('layout.staff')

@section('title', 'Staff Dashboard - PadangPro')

@section('content')
    <style>
        /* Yellow Welcome Banner */
        .welcome-banner {
            background-color: #FFD700;
            color: black;
            padding: 50px 30px;
            border-radius: 10px;
            font-size: 1.4rem;
            font-weight: bold;
            margin-bottom: 25px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.2);
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

    <!-- Soft Green Welcome Banner -->
<div class="bg-green-200 rounded-xl shadow-md p-6 mb-6">
    <h2 class="text-2xl font-bold text-black">DASHBOARD</h2>
    <p class="text-black mt-2">Welcome back, Staff! Here is an overview of recent activities.</p>
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
        <h2>Recent Staff Actions</h2>
        <ul>
            <li>Handled rental request: Pitch Beta - 18 Sep 2025</li>
            <li>Checked booking: Pitch Alpha - 20 Sep 2025</li>
            <li>Assisted customer: user123@gmail.com</li>
        </ul>
    </section>
@endsection
