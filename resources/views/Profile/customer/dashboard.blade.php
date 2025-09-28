@extends('layout.customer')

@section('title', 'Customer Dashboard - PadangPro')

@section('content')
    <style>
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
@endsection

@section('scripts')
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
@endsection
