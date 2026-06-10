<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Booking Confirmation - {{ $booking->booking_number }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            color: #333;
            margin: 0;
            padding: 20px;
            background: #f5f5f5;
        }
        .container {
            max-width: 800px;
            margin: 0 auto;
            background: white;
            padding: 40px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }
        header {
            text-align: center;
            border-bottom: 3px solid #4CAF50;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }
        h1 {
            color: #1a1a2e;
            margin: 0 0 10px 0;
        }
        .booking-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
            padding: 15px;
            background: #f9f9f9;
            border-left: 4px solid #4CAF50;
        }
        .booking-header-item {
            flex: 1;
        }
        .booking-header-label {
            font-size: 12px;
            color: #666;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 5px;
        }
        .booking-header-value {
            font-size: 18px;
            font-weight: bold;
            color: #1a1a2e;
        }
        .status-badge {
            display: inline-block;
            padding: 8px 16px;
            background: #4CAF50;
            color: white;
            border-radius: 4px;
            font-weight: bold;
            text-transform: uppercase;
        }
        .section {
            margin-bottom: 40px;
        }
        .section-title {
            font-size: 16px;
            font-weight: bold;
            color: #1a1a2e;
            border-bottom: 2px solid #4CAF50;
            padding-bottom: 10px;
            margin-bottom: 15px;
        }
        .info-row {
            display: flex;
            justify-content: space-between;
            padding: 10px 0;
            border-bottom: 1px solid #eee;
        }
        .info-label {
            font-weight: bold;
            color: #666;
        }
        .info-value {
            color: #333;
            text-align: right;
        }
        .price-section {
            background: #f9f9f9;
            padding: 15px;
            border-radius: 4px;
            margin-top: 15px;
        }
        .price-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 10px;
        }
        .price-row.total {
            border-top: 2px solid #ddd;
            padding-top: 10px;
            font-weight: bold;
            font-size: 18px;
            color: #4CAF50;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }
        th {
            background: #f0f0f0;
            padding: 10px;
            text-align: left;
            font-weight: bold;
            border-bottom: 2px solid #ddd;
        }
        td {
            padding: 10px;
            border-bottom: 1px solid #eee;
        }
        tr:last-child td {
            border-bottom: none;
        }
        .footer {
            text-align: center;
            margin-top: 40px;
            padding-top: 20px;
            border-top: 1px solid #ddd;
            color: #666;
            font-size: 12px;
        }
        .highlight {
            background: #fff3cd;
            padding: 2px 6px;
            border-radius: 3px;
        }
    </style>
</head>
<body>
    <div class="container">
        <header>
            <h1>✓ Booking Confirmation</h1>
            <p>Tourism Booking System</p>
        </header>

        <div class="booking-header">
            <div class="booking-header-item">
                <div class="booking-header-label">Booking Number</div>
                <div class="booking-header-value">{{ $booking->booking_number }}</div>
            </div>
            <div class="booking-header-item">
                <div class="booking-header-label">Confirmation Code</div>
                <div class="booking-header-value">{{ $booking->confirmation_code ?? 'PENDING' }}</div>
            </div>
            <div class="booking-header-item" style="text-align: right;">
                <div class="booking-header-label">Status</div>
                <div class="booking-header-value">
                    <span class="status-badge">{{ strtoupper($booking->status) }}</span>
                </div>
            </div>
        </div>

        <!-- Booking Information -->
        <div class="section">
            <div class="section-title">📌 Booking Information</div>
            <div class="info-row">
                <span class="info-label">Tour Package</span>
                <span class="info-value">{{ $booking->package->name }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Location</span>
                <span class="info-value">{{ $booking->package->location }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Tour Date</span>
                <span class="info-value">{{ $booking->tour_date->format('F d, Y') }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Total Guests</span>
                <span class="info-value">{{ $booking->num_guests }}</span>
            </div>
        </div>

        <!-- Guest Information -->
        <div class="section">
            <div class="section-title">👥 Guest Information</div>
            <div class="info-row">
                <span class="info-label">Guest Name</span>
                <span class="info-value">{{ $booking->user->name }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Email</span>
                <span class="info-value">{{ $booking->user->email }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Adults</span>
                <span class="info-value">{{ $booking->num_adults ?? 0 }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Children</span>
                <span class="info-value">{{ $booking->num_children ?? 0 }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Seniors</span>
                <span class="info-value">{{ $booking->num_seniors ?? 0 }}</span>
            </div>
        </div>

        <!-- Pricing Details -->
        <div class="section">
            <div class="section-title">💰 Pricing Details</div>
            <div class="price-section">
                <div class="price-row">
                    <span>Base Price</span>
                    <span>₱{{ number_format($booking->base_price ?? 0, 2) }}</span>
                </div>
                @if($booking->additional_fees > 0)
                    <div class="price-row">
                        <span>Additional Fees</span>
                        <span>₱{{ number_format($booking->additional_fees, 2) }}</span>
                    </div>
                @endif
                @if($booking->tourist_guide_fee > 0)
                    <div class="price-row">
                        <span>Tour guide fee</span>
                        <span>₱{{ number_format($booking->tourist_guide_fee, 2) }}</span>
                    </div>
                @endif
                @if($booking->discount_amount > 0)
                    <div class="price-row">
                        <span>Discount
                            @if($booking->discount_code)
                                ({{ $booking->discount_code }})
                            @endif
                        </span>
                        <span>-₱{{ number_format($booking->discount_amount, 2) }}</span>
                    </div>
                @endif
                <div class="price-row total">
                    <span>Total Amount</span>
                    <span>₱{{ number_format($booking->total_price, 2) }}</span>
                </div>
            </div>
        </div>

        <!-- Services -->
        @if($booking->services && count($booking->services) > 0)
            <div class="section">
                <div class="section-title">🎁 Additional Services</div>
                <table>
                    <thead>
                        <tr>
                            <th>Service</th>
                            <th style="text-align: right;">Price</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($booking->services as $service)
                            @php
                                $serviceLabel = 'Service';
                                $servicePrice = 0;

                                if (is_array($service)) {
                                    $serviceLabel = $service['name'] ?? ($service['key'] ?? 'Service');
                                    $servicePrice = $service['price'] ?? 0;
                                } elseif (is_string($service)) {
                                    $serviceLabel = ucfirst(str_replace('_', ' ', $service));
                                }
                            @endphp
                            <tr>
                                <td>{{ $serviceLabel }}</td>
                                <td style="text-align: right;">₱{{ number_format($servicePrice, 2) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif

        <!-- Special Requests -->
        @if($booking->special_requests)
            <div class="section">
                <div class="section-title">✉️ Special Requests</div>
                <p>{{ $booking->special_requests }}</p>
            </div>
        @endif

        <!-- Payment Status -->
        @if($booking->payment)
            <div class="section">
                <div class="section-title">💳 Payment Status</div>
                <div class="info-row">
                    <span class="info-label">Payment Method</span>
                    <span class="info-value">{{ ucfirst($booking->payment->method) }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Payment Status</span>
                    <span class="info-value">
                        <span class="highlight">{{ strtoupper($booking->payment->status) }}</span>
                    </span>
                </div>
                @if($booking->payment->paid_at)
                    <div class="info-row">
                        <span class="info-label">Paid On</span>
                        <span class="info-value">{{ $booking->payment->paid_at->format('F d, Y H:i A') }}</span>
                    </div>
                @endif
            </div>
        @endif

        <div class="footer">
            <p><strong>Thank you for booking with us!</strong></p>
            <p>This is an official booking confirmation. Please keep this document for your records.</p>
            <p>Generated on: {{ now()->format('F d, Y H:i:s A') }}</p>
            <p>For inquiries, please contact us at support@tourismBooking.com</p>
        </div>
    </div>
</body>
</html>
