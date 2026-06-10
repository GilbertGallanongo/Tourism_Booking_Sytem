<x-layout>
    <style>
        /* Dark & Simple Admin Dashboard */
        .admin-dashboard-header {
            margin-bottom: 2rem;
            background: #1a1a2e;
            padding: 2rem;
            border-radius: 0.75rem;
            border: 1px solid #2d2d4d;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.3);
        }

        .admin-title {
            font-size: 2.5rem;
            font-weight: 800;
            margin-bottom: 0.5rem;
            color: #ffffff;
        }

        .admin-subtitle {
            color: #b0b8d0;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            font-size: 1rem;
            font-weight: 600;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(7, 1fr);
            gap: 1rem;
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

        .stat-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 1rem;
        }

        .stat-label {
            font-size: 0.85rem;
            color: #8890a8;
            text-transform: uppercase;
            letter-spacing: 0.1em;
            font-weight: 700;
        }

        .stat-icon {
            font-size: 1.75rem;
            opacity: 0.8;
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

        .stat-change.positive {
            color: #81c784;
        }

        .stat-change.negative {
            color: #ef5350;
        }

        .content-section {
            margin-bottom: 2.5rem;
        }

        .section-title {
            font-size: 1.4rem;
            font-weight: 800;
            margin-bottom: 1.25rem;
            color: #ffffff;
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .section-icon {
            font-size: 2rem;
        }

        .insights-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 1.5rem;
            margin-bottom: 1.5rem;
        }

        .insight-card {
            background: #242842;
            border: 1px solid #3d3d5c;
            border-radius: 0.75rem;
            padding: 1.5rem;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.2);
            transition: all 0.2s ease;
        }

        .insight-card:hover {
            border-color: #4d4d6d;
            background: #2d2d45;
        }

        .insight-title {
            font-weight: 800;
            margin-bottom: 1rem;
            color: #ffffff;
            font-size: 1.1rem;
        }

        .metric-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0.75rem 0;
            border-bottom: 1px solid #3d3d5c;
        }

        .metric-row:last-child {
            border-bottom: none;
        }

        .metric-label {
            color: #8890a8;
            font-size: 0.9rem;
            font-weight: 600;
        }

        .metric-value {
            font-weight: 800;
            color: #ffffff;
            font-size: 1.1rem;
        }

        .progress-container {
            margin-top: 1rem;
            padding-top: 1rem;
            border-top: 1px solid #3d3d5c;
        }

        .progress-label {
            display: flex;
            justify-content: space-between;
            font-size: 0.9rem;
            margin-bottom: 0.5rem;
        }

        .progress-label-text {
            color: #8890a8;
            font-weight: 600;
        }

        .progress-label-value {
            color: #ffffff;
            font-weight: 800;
        }

        .progress-bar {
            width: 100%;
            height: 0.75rem;
            background: #1a1a2e;
            border-radius: 9999px;
            overflow: hidden;
            border: none;
        }

        .progress-fill {
            height: 100%;
            border-radius: 9999px;
            transition: width 0.3s ease;
        }

        .progress-fill.green {
            background: #81c784;
        }

        .progress-fill.blue {
            background: #64b5f6;
        }

        .progress-fill.orange {
            background: #ffb74d;
        }

        .quick-actions {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(160px, 1fr));
            gap: 1rem;
            margin-top: 1.5rem;
        }

        .action-btn {
            background: #242842;
            border: 1px solid #3d3d5c;
            color: #b0b8d0;
            padding: 1rem;
            border-radius: 0.5rem;
            text-decoration: none;
            text-align: center;
            transition: all 0.2s ease;
            font-weight: 700;
            font-size: 0.9rem;
            cursor: pointer;
        }

        .action-btn:hover {
            background: #2d2d45;
            border-color: #4d4d6d;
            color: #ffffff;
            transform: translateY(-2px);
        }

        .alert-banner {
            background: rgba(255, 193, 7, 0.4);
            border-left: 3px solid #FFB74D;
            border-radius: 0.5rem;
            padding: 1.25rem;
            margin-bottom: 2rem;
            color: #ffd700;
            font-weight: 600;
            font-size: 0.95rem;
        }

        .alert-banner.success {
            background: rgba(129, 199, 132, 0.4);
            border-left-color: #81c784;
            color: #81c784;
        }

        .dark-card {
            background: #1a1a2e;
            border: 1px solid #2d2d4d;
            border-radius: 0.75rem;
            padding: 1.5rem;
            margin-bottom: 2rem;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.3);
        }

        .kpi-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 1.5rem;
            margin-top: 1.5rem;
        }

        .kpi-box {
            background: #242842;
            border: 1px solid #3d3d5c;
            border-radius: 0.75rem;
            padding: 1.5rem;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.2);
            transition: all 0.2s ease;
        }

        .kpi-box:hover {
            border-color: #4d4d6d;
            background: #2d2d45;
        }

        .kpi-label {
            font-size: 0.85rem;
            color: #8890a8;
            margin-bottom: 0.5rem;
            text-transform: uppercase;
            letter-spacing: 0.1em;
            font-weight: 700;
        }

        .kpi-value {
            font-size: 1.75rem;
            font-weight: 800;
            color: #ffffff;
        }

        .kpi-subtext {
            font-size: 0.85rem;
            color: #8890a8;
            margin-top: 0.5rem;
            font-weight: 600;
        }
    </style>

    <!-- Header -->
    <div class="admin-dashboard-header">
        <h1 class="admin-title">📊 Administration Dashboard</h1>
        <p class="admin-subtitle">
            <span>📈</span> Complete overview of your tourism booking system
        </p>
    </div>

    <!-- Primary Stats Grid -->
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-header">
                <div>
                    <div class="stat-label">📦 Tour Packages</div>
                    <div class="stat-value">{{ $stats['packages'] }}</div>
                </div>
                <div class="stat-icon">📦</div>
            </div>
            <div class="stat-change">Available for booking</div>
        </div>

        <div class="stat-card">
            <div class="stat-header">
                <div>
                    <div class="stat-label">✈️ Total Bookings</div>
                    <div class="stat-value">{{ $stats['bookings'] }}</div>
                </div>
                <div class="stat-icon">✈️</div>
            </div>
            <div class="stat-change">All time reservations</div>
        </div>

        <div class="stat-card">
            <div class="stat-header">
                <div>
                    <div class="stat-label">⏳ Pending Approvals</div>
                    <div class="stat-value">{{ $stats['pending_bookings'] }}</div>
                </div>
                <div class="stat-icon">⏳</div>
            </div>
            <div class="stat-change positive">{{ $stats['pending_bookings'] == 0 ? 'All caught up!' : 'Needs attention' }}</div>
        </div>

        <div class="stat-card">
            <div class="stat-header">
                <div>
                    <div class="stat-label">🛎️ Tour Started</div>
                    <div class="stat-value">{{ $stats['checked_in_bookings'] }}</div>
                </div>
                <div class="stat-icon">🛎️</div>
            </div>
            <div class="stat-change">Bookings with guest arrival recorded</div>
        </div>

        <div class="stat-card">
            <div class="stat-header">
                <div>
                    <div class="stat-label">🏁 Tour Ended</div>
                    <div class="stat-value">{{ $stats['checked_out_bookings'] }}</div>
                </div>
                <div class="stat-icon">🏁</div>
            </div>
            <div class="stat-change">Bookings with departure recorded</div>
        </div>

        <div class="stat-card">
            <div class="stat-header">
                <div>
                    <div class="stat-label">✅ Paid (Not Started)</div>
                    <div class="stat-value">{{ $stats['paid_payments'] }}</div>
                </div>
                <div class="stat-icon">✅</div>
            </div>
            <div class="stat-change">Paid, awaiting tour</div>
        </div>

        <div class="stat-card">
            <div class="stat-header">
                <div>
                    <div class="stat-label">💰 Total Revenue</div>
                    <div class="stat-value" style="font-size: 1.875rem; line-height: 2.25rem;">₱{{ number_format((float) $stats['revenue'], 0) }}</div>
                </div>
                <div class="stat-icon">💰</div>
            </div>
            <div class="stat-change">From paid bookings</div>
        </div>
    </div>

    <!-- Key Performance Indicators -->
    <div class="content-section">
        <h2 class="section-title">
            <span class="section-icon">📈</span>
            Performance Metrics
        </h2>

        <div class="insights-grid">
            <!-- Booking Status Overview -->
            <div class="insight-card">
                <div class="insight-title">Booking Status Breakdown</div>
                
                <div class="metric-row">
                    <span class="metric-label">Pending Approval</span>
                    <span class="metric-value">{{ $stats['pending_bookings'] }}</span>
                </div>

                <div class="metric-row">
                    <span class="metric-label">Confirmed Bookings</span>
                    <span class="metric-value" style="color: #4CAF50;">{{ $stats['bookings'] - $stats['pending_bookings'] }}</span>
                </div>

                <div class="metric-row">
                    <span class="metric-label">Approval Rate</span>
                    <span class="metric-value">
                        @if($stats['bookings'] > 0)
                            {{ number_format((($stats['bookings'] - $stats['pending_bookings']) / $stats['bookings'] * 100), 1) }}%
                        @else
                            0%
                        @endif
                    </span>
                </div>

                <div class="progress-container">
                    <div class="progress-label">
                        <span class="progress-label-text">Pending vs Confirmed</span>
                        <span class="progress-label-value">{{ $stats['pending_bookings'] }}/{{ $stats['bookings'] }}</span>
                    </div>
                    <div class="progress-bar">
                        <div class="progress-fill orange" style="width: {{ $stats['bookings'] > 0 ? ($stats['pending_bookings'] / $stats['bookings'] * 100) : 0 }}%"></div>
                    </div>
                </div>
            </div>

            <!-- Revenue Analytics -->
            <div class="insight-card">
                <div class="insight-title">Revenue Analytics</div>
                
                <div class="metric-row">
                    <span class="metric-label">Confirmed Revenue</span>
                    <span class="metric-value">₱{{ number_format((float) $stats['revenue'], 0) }}</span>
                </div>

                <div class="metric-row">
                    <span class="metric-label">Average per Booking</span>
                    <span class="metric-value">
                        @if($stats['paid_payments'] > 0)
                            ₱{{ number_format((float) $stats['revenue'] / $stats['paid_payments'], 2) }}
                        @else
                            ₱0.00
                        @endif
                    </span>
                </div>

                <div class="metric-row">
                    <span class="metric-label">Payment Success Rate</span>
                    <span class="metric-value" style="color: #4CAF50;">
                        @if($stats['bookings'] > 0)
                            {{ number_format(($stats['paid_payments'] / $stats['bookings'] * 100), 1) }}%
                        @else
                            0%
                        @endif
                    </span>
                </div>

                <div class="progress-container">
                    <div class="progress-label">
                        <span class="progress-label-text">Paid vs Unpaid</span>
                        <span class="progress-label-value">{{ $stats['paid_payments'] }}/{{ $stats['bookings'] }}</span>
                    </div>
                    <div class="progress-bar">
                        <div class="progress-fill green" style="width: {{ $stats['bookings'] > 0 ? ($stats['paid_payments'] / $stats['bookings'] * 100) : 0 }}%"></div>
                    </div>
                </div>
            </div>

            <!-- Package Management -->
            <div class="insight-card">
                <div class="insight-title">Package Portfolio</div>
                
                <div class="metric-row">
                    <span class="metric-label">Total Packages</span>
                    <span class="metric-value">{{ $stats['packages'] }}</span>
                </div>

                <div class="metric-row">
                    <span class="metric-label">Active Packages</span>
                    <span class="metric-value" style="color: #4CAF50;">{{ $stats['packages'] }}</span>
                </div>

                <div class="metric-row">
                    <span class="metric-label">Avg Bookings/Package</span>
                    <span class="metric-value">
                        @if($stats['packages'] > 0)
                            {{ number_format(($stats['bookings'] / $stats['packages']), 1) }}
                        @else
                            0
                        @endif
                    </span>
                </div>

                <div class="progress-container">
                    <div class="progress-label">
                        <span class="progress-label-text">Package Utilization</span>
                        <span class="progress-label-value">
                            @if($stats['packages'] > 0)
                                {{ number_format(min(100, ($stats['bookings'] / $stats['packages'] / 10) * 100), 1) }}%
                            @else
                                0%
                            @endif
                        </span>
                    </div>
                    <div class="progress-bar">
                        <div class="progress-fill blue" style="width: {{ $stats['packages'] > 0 ? min(100, ($stats['bookings'] / $stats['packages'] / 10) * 100) : 0 }}%"></div>
                    </div>
                </div>
            </div>
        </div>
    <!-- Additional Metrics -->
    <div class="content-section">
        <h2 class="section-title">
            <span class="section-icon">👥</span>
            Customer Engagement
        </h2>

        <div class="insights-grid">
            <div class="insight-card">
                <div class="insight-title">Customer Satisfaction</div>
                
                <div class="metric-row">
                    <span class="metric-label">Average Rating</span>
                    <span class="metric-value" style="color: #FFD700;">
                        ⭐ {{ $stats['avgRating'] }}/5.0
                    </span>
                </div>

                <div class="metric-row">
                    <span class="metric-label">Total Reviews</span>
                    <span class="metric-value">{{ $stats['totalReviews'] }}</span>
                </div>

                <div class="metric-row">
                    <span class="metric-label">Active Users</span>
                    <span class="metric-value" style="color: #4CAF50;">{{ $stats['activeUsers'] }}</span>
                </div>

                <div class="progress-container">
                    <div class="progress-label">
                        <span class="progress-label-text">Customer Satisfaction</span>
                        <span class="progress-label-value">{{ round(($stats['avgRating'] / 5) * 100, 1) }}%</span>
                    </div>
                    <div class="progress-bar">
                        <div class="progress-fill green" style="width: {{ round(($stats['avgRating'] / 5) * 100, 1) }}%"></div>
                    </div>
                </div>
            </div>

            <div class="insight-card">
                <div class="insight-title">Top Categories</div>
                
                @forelse($topCategories as $category)
                    <div class="metric-row" style="margin-bottom: 0.75rem;">
                        <span class="metric-label">{{ $categoryLabels[$category->category] ?? ucfirst($category->category) }}</span>
                        <span class="metric-value" style="color: #4CAF50;">{{ $category->total_bookings }} bookings</span>
                    </div>
                    @if (! $loop->last)
                        <hr style="border-color: rgba(255,255,255,0.08); margin: 0.75rem 0;" />
                    @endif
                @empty
                    <div class="metric-row">
                        <span class="metric-label">No booking data yet.</span>
                    </div>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Top Packages -->
    @if($topPackages->isNotEmpty())
    <div class="content-section">
        <h2 class="section-title">
            <span class="section-icon">🏆</span>
            Top Performing & Highly Rated Packages
        </h2>

        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 1.5rem;">
            @foreach($topPackages as $package)
                <div class="insight-card">
                    <div class="insight-title">{{ $package->name ?? 'Unknown Package' }}</div>
                    
                    <div class="metric-row">
                        <span class="metric-label">Location</span>
                        <span class="metric-value" style="font-size: 1rem;">{{ $package->location ?? 'Unknown' }}</span>
                    </div>

                    <div class="metric-row">
                        <span class="metric-label">Total Bookings</span>
                        <span class="metric-value">{{ $package->bookings_count ?? 0 }}</span>
                    </div>

                    <div class="metric-row">
                        <span class="metric-label">Rating</span>
                        <span class="metric-value" style="color: #FFD700;">
                            @if($package->average_rating > 0)
                                ⭐ {{ number_format($package->average_rating, 1) }}/5.0
                            @else
                                No ratings
                            @endif
                        </span>
                    </div>

                    <div class="metric-row">
                        <span class="metric-label">Review Count</span>
                        <span class="metric-value">{{ $package->reviews_count ?? 0 }} reviews</span>
                    </div>

                    <div class="metric-row">
                        <span class="metric-label">Price</span>
                        <span class="metric-value" style="color: #4CAF50;">₱{{ number_format($package->price ?? 0, 2) }}</span>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
    @endif

    <!-- Famous Tourist Spots -->
    @if($famousTouristSpots->isNotEmpty())
    <div class="content-section">
        <h2 class="section-title">
            <span class="section-icon">🗺️</span>
            Famous Tourist Spots
        </h2>

        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 1.5rem;">
            @foreach($famousTouristSpots->take(5) as $spot)
                <div class="insight-card">
                    <div class="insight-title">{{ $spot->name }}</div>

                    <div class="metric-row">
                        <span class="metric-label">Location</span>
                        <span class="metric-value" style="font-size: 0.875rem;">{{ $spot->location }}</span>
                    </div>

                    <div class="metric-row">
                        <span class="metric-label">Description</span>
                        <span class="metric-value" style="font-size: 0.875rem;">{{ Str::limit($spot->description, 50) }}</span>
                    </div>

                    <div class="metric-row">
                        <span class="metric-label">Status</span>
                        <span class="metric-value" style="color: {{ $spot->is_active ? '#81c784' : '#8890a8' }};">
                            {{ $spot->is_active ? 'Active' : 'Inactive' }}
                        </span>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
    @endif

    <!-- Recent Bookings -->
    @if($recentBookings->isNotEmpty())
    <style>
        .recent-bookings-table {
            background: #1f1f3a;
            border: 1px solid #3d3d5c;
            border-radius: 0.75rem;
            overflow: hidden;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.2);
        }

        .recent-bookings-header {
            display: grid;
            grid-template-columns: 1fr 1fr 1fr 1fr 1fr 1fr 1fr;
            gap: 1rem;
            padding: 1.25rem;
            background: linear-gradient(135deg, #2d3561 0%, #3d4571 100%);
            border-bottom: 2px solid #4d5d8d;
            font-weight: 700;
            color: #ffffff;
            font-size: 0.875rem;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        .recent-bookings-rows {
            max-height: 400px;
            overflow-y: auto;
        }

        .recent-bookings-row {
            display: grid;
            grid-template-columns: 1fr 1fr 1fr 1fr 1fr 1fr 1fr;
            gap: 1rem;
            padding: 1.25rem;
            border-bottom: 1px solid #3d3d5c;
            align-items: center;
            transition: all 0.2s ease;
            background-color: transparent;
        }

        .recent-bookings-row:nth-child(odd) {
            background: rgba(45, 53, 97, 0.4);
        }

        .recent-bookings-row:nth-child(even) {
            background: rgba(36, 40, 66, 0.4);
        }

        .recent-bookings-row:hover {
            background: rgba(61, 93, 157, 0.5);
        }

        .recent-bookings-row .guest-info {
            display: flex;
            flex-direction: column;
            gap: 0.25rem;
        }

        .recent-bookings-row .guest-name {
            color: #ffffff;
            font-weight: 600;
            font-size: 0.95rem;
        }

        .recent-bookings-row .guest-email {
            color: #9db3d1;
            font-size: 0.75rem;
        }

        .recent-bookings-row .cell-text {
            color: #ffffff;
            font-weight: 500;
        }

        .recent-bookings-row .cell-secondary {
            color: #b0b8d0;
            font-size: 0.9rem;
        }

        .recent-bookings-row .cell-amount {
            color: #7dd87d;
            font-weight: 700;
            font-size: 1rem;
        }
    </style>
    <div class="content-section">
        <h2 class="section-title">
            <span class="section-icon">📋</span>
            Recent Bookings
        </h2>

        <div class="recent-bookings-table">
            <div class="recent-bookings-header">
                <div>Guest</div>
                <div>Package</div>
                <div>Tour Date</div>
                <div>Tour Start</div>
                <div>Tour End</div>
                <div>Status</div>
                <div>Amount</div>
            </div>
            <div class="recent-bookings-rows">
                @foreach($recentBookings->take(10) as $booking)
                    <div class="recent-bookings-row">
                        <div class="guest-info">
                            <div class="guest-name">{{ $booking->user->name }}</div>
                            <div class="guest-email">{{ $booking->user->email }}</div>
                        </div>
                        <div class="cell-text">{{ Str::limit($booking->package->name, 20) }}</div>
                        <div class="cell-secondary">{{ optional($booking->tour_date)->format('M d, Y') }}</div>
                        <div class="cell-secondary">{{ optional($booking->tour_start_date)->format('M d, Y') ?? 'Not set' }}</div>
                        <div class="cell-secondary">{{ optional($booking->tour_end_date)->format('M d, Y') ?? 'Not set' }}</div>
                        <div>
                            <span class="booking-badge badge-{{ $booking->status }}" style="padding: 0.25rem 0.5rem; font-size: 0.75rem;">{{ ucfirst($booking->status) }}</span>
                        </div>
                        <div class="cell-amount">₱{{ number_format($booking->total_price, 2) }}</div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
    @endif

    <!-- Recent Reviews -->
    @if($recentReviews->isNotEmpty())
    <style>
        .review-card {
            background: linear-gradient(135deg, rgba(45, 53, 97, 0.6) 0%, rgba(36, 40, 66, 0.6) 100%);
            border: 1px solid #3d3d5c;
            border-radius: 0.75rem;
            padding: 1.5rem;
            transition: all 0.2s ease;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.2);
        }

        .review-card:hover {
            border-color: #4d5d8d;
            background: linear-gradient(135deg, rgba(61, 93, 157, 0.6) 0%, rgba(45, 53, 97, 0.6) 100%);
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.3);
        }

        .review-card-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 1rem;
            gap: 1rem;
        }

        .review-card-info h3 {
            color: #ffffff;
            font-size: 1rem;
            font-weight: 700;
            margin: 0 0 0.25rem 0;
        }

        .review-card-info p {
            color: #9db3d1;
            font-size: 0.875rem;
            margin: 0;
            font-weight: 500;
        }

        .review-card-rating {
            font-size: 1.75rem;
            color: #FFD700;
            font-weight: 700;
            white-space: nowrap;
        }

        .review-card-comment {
            color: #b0b8d0;
            font-size: 0.9rem;
            line-height: 1.5;
            margin: 0.75rem 0;
        }

        .review-card-date {
            color: #7d8fa3;
            font-size: 0.8rem;
            font-weight: 500;
        }
    </style>
    <div class="content-section">
        <h2 class="section-title">
            <span class="section-icon">⭐</span>
            Recent Customer Reviews
        </h2>

        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 1.5rem;">
            @foreach($recentReviews->take(6) as $review)
                <div class="review-card">
                    <div class="review-card-header">
                        <div class="review-card-info">
                            <h3>{{ $review->tourPackage?->name ?? 'Unknown Package' }}</h3>
                            <p>by {{ $review->user?->name ?? 'Unknown User' }}</p>
                        </div>
                        <div class="review-card-rating">⭐ {{ $review->rating }}</div>
                    </div>
                    <p class="review-card-comment">{{ Str::limit($review->comment ?? 'No comment', 120) }}</p>
                    <p class="review-card-date">{{ $review->created_at?->diffForHumans() ?? 'Recently' }}</p>
                </div>
            @endforeach
        </div>
    </div>
    @endif

</x-layout>

<style>
    .booking-badge {
        display: inline-block;
        padding: 0.25rem 0.75rem;
        border-radius: 9999px;
        font-size: 0.75rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.05em;
    }

    .badge-pending {
        background: rgba(255, 193, 7, 0.2);
        color: #FFC107;
    }

    .badge-approved {
        background: rgba(76, 175, 80, 0.2);
        color: #4CAF50;
    }

    .badge-paid {
        background: rgba(76, 175, 80, 0.2);
        color: #4CAF50;
    }

    .badge-cancelled {
        background: rgba(244, 67, 54, 0.2);
        color: #F44336;
    }

    .section-card {
        background: rgba(75, 86, 148, 0.1);
        border: 1px solid rgba(75, 86, 148, 0.2);
        border-radius: 0.75rem;
        padding: 1.5rem;
    }
</style>
