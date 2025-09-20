@extends('layout.admin')

@section('title', 'Administrator Dashboard - PadangPro')

@section('content')
<style>
    /* Yellow Welcome Banner */
    .welcome-banner {
        background-color: #FFD700;
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

<!-- Yellow Welcome Banner -->
<div class="welcome-banner">
    WELCOME, {{ Auth::user()->name ?? 'Administrator' }}
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
@endsection
