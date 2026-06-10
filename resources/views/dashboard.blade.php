<x-layout>
    <style>
        /* Dark & Simple Dashboard */
        .dashboard-header {
            margin-bottom: 2rem;
            background: #1a1a2e;
            padding: 2rem;
            border-radius: 0.75rem;
            border: 1px solid #2d2d4d;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.3);
        }

        .dashboard-title {
            font-size: 2.5rem;
            font-weight: 800;
            margin-bottom: 0.5rem;
            color: #ffffff;
        }

        .dashboard-subtitle {
            color: #b0b8d0;
            margin-bottom: 0;
            font-size: 1rem;
            font-weight: 600;
        }

        .dashboard-actions {
            display: flex;
            gap: 1rem;
            flex-wrap: wrap;
            margin-bottom: 0;
            margin-top: 1rem;
        }

        .dashboard-actions .navbtn {
            background: #4CAF50;
            border: none;
            padding: 0.75rem 1.5rem;
            font-weight: 700;
            border-radius: 0.5rem;
            transition: all 0.2s ease;
            font-size: 0.95rem;
            color: white;
            cursor: pointer;
        }

        .dashboard-actions .navbtn:hover {
            background: #45a049;
            transform: translateY(-2px);
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2.5rem;
        }

        .stat-card {
            background: #242842;
            border: 1px solid #3d3d5c;
            border-radius: 0.75rem;
            padding: 1.5rem;
            transition: all 0.2s ease;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.2);
        }

        .stat-card:hover {
            background: #2d2d45;
            border-color: #4d4d6d;
            transform: translateY(-2px);
        }

        .stat-label {
            font-size: 0.85rem;
            color: #8890a8;
            text-transform: uppercase;
            letter-spacing: 0.1em;
            font-weight: 700;
            margin-bottom: 0.5rem;
        }

        .stat-value {
            font-size: 2.5rem;
            font-weight: 800;
            color: #ffffff;
            line-height: 1;
            margin-bottom: 0.5rem;
        }

        .stat-change {
            font-size: 0.9rem;
            color: #8890a8;
            font-weight: 600;
        }

        .content-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2.5rem;
        }

        .section-card {
            background: #242842;
            border: 1px solid #3d3d5c;
            border-radius: 0.75rem;
            padding: 1.75rem;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.2);
            transition: all 0.2s ease;
        }

        .section-card:hover {
            border-color: #4d4d6d;
            background: #2d2d45;
        }

        .section-title {
            font-size: 1.4rem;
            font-weight: 800;
            margin-bottom: 1.25rem;
            color: #ffffff;
        }

        .section-icon {
            margin-right: 0.5rem;
        }

        .booking-list {
            display: flex;
            flex-direction: column;
            gap: 1rem;
        }

        .booking-item {
            background: #1f2333;
            border-left: 3px solid #4CAF50;
            padding: 1.25rem;
            border-radius: 0.5rem;
            transition: all 0.2s ease;
        }

        .booking-item:hover {
            background: #262c3d;
            transform: translateX(4px);
        }

        .booking-title {
            font-weight: 800;
            color: #ffffff;
            margin-bottom: 0.5rem;
            font-size: 1rem;
        }

        .booking-meta {
            display: flex;
            gap: 1.5rem;
            flex-wrap: wrap;
            font-size: 0.85rem;
            color: #8890a8;
            margin-bottom: 0.5rem;
            font-weight: 600;
        }

        .booking-badge {
            display: inline-block;
            padding: 0.3rem 0.75rem;
            border-radius: 9999px;
            font-size: 0.75rem;
            font-weight: 700;
            text-transform: uppercase;
        }

        .badge-pending {
            background: rgba(255, 193, 7, 0.2);
            color: #ffc107;
        }

        .badge-approved {
            background: rgba(129, 199, 132, 0.2);
            color: #81c784;
        }

        .badge-paid {
            background: rgba(129, 199, 132, 0.2);
            color: #81c784;
        }

        .badge-cancelled {
            background: rgba(239, 83, 80, 0.2);
            color: #ef5350;
        }

        .package-card {
            background: #1f2333;
            border: 1px solid #3d3d5c;
            border-radius: 0.75rem;
            padding: 1.25rem;
            transition: all 0.2s ease;
            cursor: pointer;
        }

        .package-card:hover {
            background: #262c3d;
            border-color: #4d4d6d;
            transform: translateY(-2px);
        }

        .package-name {
            font-weight: 800;
            color: #ffffff;
            margin-bottom: 0.5rem;
            font-size: 1rem;
        }

        .package-location {
            font-size: 0.9rem;
            color: #8890a8;
            margin-bottom: 0.75rem;
            font-weight: 600;
        }

        .package-price {
            font-size: 1.3rem;
            font-weight: 800;
            color: #81c784;
        }

        .empty-state {
            text-align: center;
            padding: 2rem 1rem;
            color: #8890a8;
        }

        .empty-state-icon {
            font-size: 3rem;
            margin-bottom: 0.75rem;
        }

        .chart-container {
            background: #242842;
            border: 1px solid #3d3d5c;
            border-radius: 0.75rem;
            padding: 2rem;
            margin-bottom: 2.5rem;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.2);
        }

        .chart-title {
            font-size: 1.4rem;
            font-weight: 800;
            margin-bottom: 1.5rem;
            color: #ffffff;
        }

        .view-all-btn {
            display: inline-block;
            margin-top: 1rem;
            padding: 0.75rem 1.5rem;
            background: #4CAF50;
            border: none;
            color: white;
            border-radius: 0.5rem;
            text-decoration: none;
            transition: all 0.2s ease;
            font-size: 0.95rem;
            font-weight: 700;
            cursor: pointer;
        }

        .view-all-btn:hover {
            background: #45a049;
            transform: translateY(-2px);
        }

        .progress-bar {
            width: 100%;
            height: 0.75rem;
            background: #1a1a2e;
            border-radius: 9999px;
            overflow: hidden;
            margin: 0.75rem 0;
        }

        .progress-fill {
            height: 100%;
            border-radius: 9999px;
            transition: width 0.3s ease;
            background: #81c784;
        }

        .content-section {
            margin-bottom: 2.5rem;
        }

        .content-section .section-title {
            font-size: 1.4rem;
        }
    </style>

    <!-- Header Section -->
    <div class="dashboard-header">
        <h1 class="dashboard-title">Welcome back, {{ $user->name ?? 'Traveler' }}! 👋</h1>
        <p class="dashboard-subtitle">Here's what's happening with your bookings and travel plans</p>

        <div class="dashboard-actions">
            <a href="{{ route('packages.index') }}" class="navbtn">Browse Packages</a>
            <a href="{{ route('reservations.index') }}" class="navbtn">My Reservations</a>
            <a href="{{ route('famous-tourist-spots.index') }}" class="navbtn">Famous Tourist Spots</a>
        </div>
    </div>

    <!-- Stats Grid -->
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-label">📦 Available Packages</div>
            <div class="stat-value">{{ $stats['packages'] }}</div>
            <div class="stat-change">Ready to explore</div>
        </div>
        <div class="stat-card">
            <div class="stat-label">✈️ Total Bookings</div>
            <div class="stat-value">{{ $stats['bookings'] }}</div>
            <div class="stat-change">All your trips</div>
        </div>
        <div class="stat-card">
            <div class="stat-label">⏳ Pending Trips</div>
            <div class="stat-value">{{ $stats['pending_bookings'] }}</div>
            <div class="stat-change">Awaiting approval</div>
        </div>
        <div class="stat-card">
            <div class="stat-label">✅ Paid Bookings</div>
            <div class="stat-value">{{ $stats['paid_payments'] }}</div>
            <div class="stat-change">Confirmed trips</div>
        </div>
        <div class="stat-card">
            <div class="stat-label">💰 Total Spent</div>
            <div class="stat-value">PHP {{ number_format((float) $stats['revenue'], 0) }}</div>
            <div class="stat-change">Lifetime value</div>
        </div>
    </div>

    <!-- Main Content Grid -->
    <div class="content-grid">
        <!-- Upcoming Trips Section -->
        <div class="section-card">
            <h2 class="section-title">
                <span class="section-icon">📅</span>
                Upcoming Trips
            </h2>
            <p class="text-sm" style="color: var(--palette-secondary); margin-bottom: 1rem;">Your next adventures</p>

            @if($recentBookings->isEmpty())
                <div class="empty-state">
                    <div class="empty-state-icon">🌍</div>
                    <p>No upcoming bookings yet</p>
                    <p style="font-size: 0.875rem; margin-top: 0.5rem;">Start exploring tours to book your next adventure</p>
                </div>
            @else
                <div class="booking-list">
                    @foreach($recentBookings->take(5) as $booking)
                        <div class="booking-item">
                            <div class="booking-title">{{ $booking->package->name }}</div>
                            <div class="booking-meta">
                                <span>📍 {{ $booking->package->location }}</span>
                                <span>📆 {{ $booking->tour_date->format('M d, Y') }}</span>
                                <span>👥 {{ $booking->num_guests }} guest{{ $booking->num_guests > 1 ? 's' : '' }}</span>
                            </div>
                            <div>
                                <span class="booking-badge badge-{{ $booking->status }}">
                                    {{ ucfirst($booking->status) }}
                                </span>
                            </div>
                        </div>
                    @endforeach
                </div>
                @if($recentBookings->count() > 5)
                    <a href="{{ route('reservations.index') }}" class="view-all-btn">View all reservations →</a>
                @endif
            @endif
        </div>

    <!-- Booking Statistics -->
    @if($stats['bookings'] > 0)
    <div class="chart-container">
        <h3 class="chart-title">Booking Summary</h3>
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(150px, 1fr)); gap: 1.5rem;">
            @php
                $approvedCount = $recentBookings->where('status', 'approved')->count();
                $pendingCount = $recentBookings->where('status', 'pending')->count();
                $cancelledCount = $recentBookings->where('status', 'cancelled')->count();
            @endphp
            
            <div>
                <div style="font-size: 0.875rem; color: var(--palette-secondary); margin-bottom: 0.5rem;">Approved</div>
                <div style="font-size: 1.5rem; font-weight: 700; color: #4CAF50;">{{ $approvedCount }}</div>
                <div class="progress-bar">
                    <div class="progress-fill" style="width: {{ $stats['bookings'] > 0 ? ($approvedCount / $stats['bookings'] * 100) : 0 }}%"></div>
                </div>
            </div>

            <div>
                <div style="font-size: 0.875rem; color: var(--palette-secondary); margin-bottom: 0.5rem;">Pending</div>
                <div style="font-size: 1.5rem; font-weight: 700; color: #FFC107;">{{ $pendingCount }}</div>
                <div class="progress-bar">
                    <div class="progress-fill" style="width: {{ $stats['bookings'] > 0 ? ($pendingCount / $stats['bookings'] * 100) : 0 }}%; background: linear-gradient(90deg, #FFC107, #FFB300);"></div>
                </div>
            </div>

            <div>
                <div style="font-size: 0.875rem; color: var(--palette-secondary); margin-bottom: 0.5rem;">Cancelled</div>
                <div style="font-size: 1.5rem; font-weight: 700; color: #F44336;">{{ $cancelledCount }}</div>
                <div class="progress-bar">
                    <div class="progress-fill" style="width: {{ $stats['bookings'] > 0 ? ($cancelledCount / $stats['bookings'] * 100) : 0 }}%; background: linear-gradient(90deg, #F44336, #E53935);"></div>
                </div>
            </div>
        </div>
    </div>
    @endif
</x-layout>
