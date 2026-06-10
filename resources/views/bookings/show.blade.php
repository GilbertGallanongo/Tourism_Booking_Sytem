<x-layout>
    <style>
        .booking-detail-section {
            background: #242842;
            border: 1px solid #3d3d5c;
            border-radius: 0.75rem;
            padding: 1.75rem;
            margin-bottom: 1.5rem;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.2);
        }

        .booking-detail-title {
            font-size: 1.3rem;
            font-weight: 800;
            color: #ffffff;
            margin-bottom: 1.25rem;
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .booking-detail-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0.875rem 0;
            border-bottom: 1px solid #3d3d5c;
        }

        .booking-detail-row:last-child {
            border-bottom: none;
        }

        .booking-detail-label {
            color: #8890a8;
            font-weight: 700;
            font-size: 0.95rem;
            text-transform: uppercase;
            letter-spacing: 0.08em;
        }

        .booking-detail-value {
            color: #ffffff;
            font-weight: 700;
            font-size: 1rem;
        }

        .booking-status-badge {
            display: inline-block;
            padding: 0.4rem 1rem;
            border-radius: 9999px;
            font-weight: 800;
            font-size: 0.85rem;
            text-transform: uppercase;
        }

        .badge-pending {
            background: rgba(255, 193, 7, 0.2);
            color: #ffc107;
        }

        .badge-confirmed {
            background: rgba(129, 199, 132, 0.2);
            color: #81c784;
        }

        .badge-cancelled {
            background: rgba(239, 83, 80, 0.2);
            color: #ef5350;
        }

        .badge-completed {
            background: rgba(100, 181, 246, 0.2);
            color: #64b5f6;
        }

        .booking-price-summary {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1rem;
            margin-top: 1rem;
        }

        .price-item {
            background: #1f2333;
            padding: 1rem;
            border-radius: 0.5rem;
            border: 1px solid #3d3d5c;
        }

        .price-label {
            color: #8890a8;
            font-size: 0.85rem;
            font-weight: 600;
            margin-bottom: 0.5rem;
        }

        .price-value {
            color: #81c784;
            font-size: 1.4rem;
            font-weight: 800;
        }

        .action-buttons {
            display: flex;
            gap: 1rem;
            flex-wrap: wrap;
            margin-top: 1.5rem;
        }

        .btn-action {
            padding: 0.75rem 1.5rem;
            border-radius: 0.5rem;
            border: none;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.2s ease;
            text-decoration: none;
            display: inline-block;
        }

        .btn-primary {
            background: #4CAF50;
            color: white;
        }

        .btn-primary:hover {
            background: #45a049;
            transform: translateY(-2px);
        }

        .btn-danger {
            background: #ef5350;
            color: white;
        }

        .btn-danger:hover {
            background: #e53935;
            transform: translateY(-2px);
        }

        .btn-secondary {
            background: #64b5f6;
            color: white;
        }

        .btn-secondary:hover {
            background: #42a5f5;
            transform: translateY(-2px);
        }

        .guest-list {
            display: grid;
            gap: 1rem;
        }

        .guest-card {
            background: #1f2333;
            padding: 1rem;
            border-radius: 0.5rem;
            border-left: 3px solid #64b5f6;
        }

        .guest-name {
            font-weight: 800;
            color: #ffffff;
            margin-bottom: 0.5rem;
        }

        .guest-info {
            font-size: 0.85rem;
            color: #8890a8;
            display: flex;
            gap: 1.5rem;
        }

        .notes-box {
            background: #1f2333;
            padding: 1rem;
            border-radius: 0.5rem;
            color: #d0d8f0;
            font-family: monospace;
            font-size: 0.85rem;
            max-height: 200px;
            overflow-y: auto;
            border: 1px solid #3d3d5c;
        }

        .timeline-item {
            display: flex;
            gap: 1rem;
            padding: 1rem 0;
            border-bottom: 1px solid #3d3d5c;
        }

        .timeline-item:last-child {
            border-bottom: none;
        }

        .timeline-date {
            color: #8890a8;
            font-weight: 700;
            min-width: 150px;
        }

        .timeline-event {
            flex: 1;
            color: #d0d8f0;
        }
    </style>

    <div class="booking-detail-section" style="background: linear-gradient(135deg, #1f2a3d 0%, #242842 100%); border: 1px solid #3d4d6d;">
        <div style="display: flex; justify-content: space-between; align-items: center;">
            <div>
                <h1 class="booking-detail-title" style="margin-bottom: 0.5rem;">
                    📌 Booking Details
                </h1>
                <p style="color: #8890a8; font-size: 0.95rem;">
                    {{ $booking->booking_number }}
                </p>
            </div>
            <span class="booking-status-badge badge-{{ strtolower($booking->status) }}">
                {{ $booking->status_label }}
            </span>
        </div>
    </div>

    <!-- Booking Summary -->
    <div class="booking-detail-section">
        <div class="booking-detail-title">🎫 Booking Information</div>
        
        <div class="booking-detail-row">
            <span class="booking-detail-label">Confirmation Code</span>
            <span class="booking-detail-value">{{ $booking->confirmation_code ?? 'Pending' }}</span>
        </div>
        <div class="booking-detail-row">
            <span class="booking-detail-label">Reference Code</span>
            <span class="booking-detail-value">{{ $booking->reference_code ?? 'Pending' }}</span>
        </div>
        <div class="booking-detail-row">
            <span class="booking-detail-label">Tour Package</span>
            <span class="booking-detail-value">{{ $booking->package->name }}</span>
        </div>
        <div class="booking-detail-row">
            <span class="booking-detail-label">Tour Date</span>
            <span class="booking-detail-value">{{ $booking->tour_date->format('M d, Y') }}</span>
        </div>
        <div class="booking-detail-row">
            <span class="booking-detail-label">Days Remaining</span>
            <span class="booking-detail-value">
                @if($booking->remaining_days > 0)
                    {{ $booking->remaining_days }} days
                @elseif($booking->remaining_days == 0)
                    <span style="color: #ffc107;">Today</span>
                @else
                    <span style="color: #64b5f6;">Completed</span>
                @endif
            </span>
        </div>
        <div class="booking-detail-row">
            <span class="booking-detail-label">Location</span>
            <span class="booking-detail-value">{{ $booking->package->location }}</span>
        </div>
    </div>

    <!-- Guest Information -->
    <div class="booking-detail-section">
        <div class="booking-detail-title">👥 Guest Information</div>
        
        <div class="booking-detail-row">
            <span class="booking-detail-label">Total Guests</span>
            <span class="booking-detail-value">{{ $booking->num_guests }}</span>
        </div>
        <div class="booking-detail-row">
            <span class="booking-detail-label">Adults</span>
            <span class="booking-detail-value">{{ $booking->num_adults }}</span>
        </div>
        <div class="booking-detail-row">
            <span class="booking-detail-label">Children</span>
            <span class="booking-detail-value">{{ $booking->num_children }}</span>
        </div>
        <div class="booking-detail-row">
            <span class="booking-detail-label">Seniors</span>
            <span class="booking-detail-value">{{ $booking->num_seniors }}</span>
        </div>

        @if($booking->guest_details && count($booking->guest_details) > 0)
            <div style="margin-top: 1rem;">
                <div style="color: #8890a8; font-size: 0.9rem; margin-bottom: 0.75rem; text-transform: uppercase; letter-spacing: 0.08em;">Passenger Details</div>
                <div class="guest-list">
                    @foreach($booking->guest_details as $guest)
                        <div class="guest-card">
                            <div class="guest-name">{{ $guest['name'] ?? 'Guest' }}</div>
                            <div class="guest-info">
                                @if($guest['email'])
                                    <span>📧 {{ $guest['email'] }}</span>
                                @endif
                                @if($guest['age'])
                                    <span>🎂 Age {{ $guest['age'] }}</span>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
    </div>

    <!-- Promo Package -->
    @if($booking->promoPackage)
        <div class="booking-detail-section" style="border-left: 4px solid #ffc107;">
            <div class="booking-detail-title">🎉 Promo Package Applied</div>
            
            <div class="booking-detail-row">
                <span class="booking-detail-label">Promo Name</span>
                <span class="booking-detail-value" style="color: #ffc107;">{{ $booking->promoPackage->name }}</span>
            </div>
            @if($booking->promoPackage->description)
                <div class="booking-detail-row">
                    <span class="booking-detail-label">Description</span>
                    <span class="booking-detail-value">{{ $booking->promoPackage->description }}</span>
                </div>
            @endif
            <div class="booking-detail-row">
                <span class="booking-detail-label">Discount</span>
                <span class="booking-detail-value" style="color: #ffc107;">{{ $booking->promoPackage->discount_percentage }}% OFF</span>
            </div>
            <div class="booking-detail-row">
                <span class="booking-detail-label">Valid Until</span>
                <span class="booking-detail-value">{{ $booking->promoPackage->end_date->format('M d, Y') }}</span>
            </div>
        </div>
    @endif

    <!-- Price Summary -->
    <div class="booking-detail-section">
        <div class="booking-detail-title">💰 Pricing Details</div>
        
        <div class="booking-detail-row">
            <span class="booking-detail-label">Base Price</span>
            <span class="booking-detail-value">₱{{ number_format($booking->base_price ?? 0, 2) }}</span>
        </div>
        <div class="booking-detail-row">
            <span class="booking-detail-label">Additional Fees</span>
            <span class="booking-detail-value">₱{{ number_format($booking->additional_fees ?? 0, 2) }}</span>
        </div>
        @if($booking->tourist_guide_fee > 0)
            <div class="booking-detail-row">
                <span class="booking-detail-label">Tour guide fee</span>
                <span class="booking-detail-value">₱{{ number_format($booking->tourist_guide_fee, 2) }}</span>
            </div>
        @endif
        @if($booking->discount_amount > 0)
            <div class="booking-detail-row">
                <span class="booking-detail-label">Discount
                    @if($booking->discount_code)
                        ({{ $booking->discount_code }})
                    @endif
                </span>
                <span class="booking-detail-value" style="color: #81c784;">-₱{{ number_format($booking->discount_amount, 2) }}</span>
            </div>
        @endif
        <div class="booking-detail-row" style="border-top: 2px solid #4d4d6d; padding-top: 1rem; margin-top: 0.5rem;">
            <span class="booking-detail-label">Total Price</span>
            <span class="booking-detail-value" style="font-size: 1.2rem; color: #81c784;">₱{{ number_format($booking->total_price, 2) }}</span>
        </div>

        @if($booking->payment_plan !== 'full')
            <div class="booking-detail-row">
                <span class="booking-detail-label">Payment Plan</span>
                <span class="booking-detail-value">{{ ucfirst($booking->payment_plan) }} - {{ $booking->payment_installments }} payments</span>
            </div>
        @endif
    </div>

    <!-- Services -->
    @if($booking->services && count($booking->services) > 0)
        <div class="booking-detail-section">
            <div class="booking-detail-title">🎁 Additional Services</div>
            
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
                <div class="booking-detail-row">
                    <span class="booking-detail-label">{{ $serviceLabel }}</span>
                    <span class="booking-detail-value">₱{{ number_format($servicePrice, 2) }}</span>
                </div>
            @endforeach
        </div>
    @endif

    <!-- Special Requests -->
    @if($booking->special_requests)
        <div class="booking-detail-section">
            <div class="booking-detail-title">✉️ Special Requests</div>
            <div style="color: #d0d8f0; line-height: 1.6;">{{ $booking->special_requests }}</div>
        </div>
    @endif

    <!-- Payment Status -->
    @if($booking->payment)
        <div class="booking-detail-section">
            <div class="booking-detail-title">💳 Payment Status</div>
            
            <div class="booking-detail-row">
                <span class="booking-detail-label">Amount</span>
                <span class="booking-detail-value">₱{{ number_format($booking->payment->amount, 2) }}</span>
            </div>
            <div class="booking-detail-row">
                <span class="booking-detail-label">Method</span>
                <span class="booking-detail-value">{{ ucfirst($booking->payment->method) }}</span>
            </div>
            <div class="booking-detail-row">
                <span class="booking-detail-label">Status</span>
                <span class="booking-detail-value">
                    @if($booking->payment->status === 'paid')
                        <span style="color: #81c784;">✓ Paid</span>
                    @elseif($booking->payment->status === 'pending')
                        <span style="color: #ffc107;">⏳ Pending</span>
                    @else
                        <span style="color: #ef5350;">✗ {{ ucfirst($booking->payment->status) }}</span>
                    @endif
                </span>
            </div>
            @if($booking->payment->paid_at)
                <div class="booking-detail-row">
                    <span class="booking-detail-label">Paid Date</span>
                    <span class="booking-detail-value">{{ $booking->payment->paid_at->format('M d, Y H:i') }}</span>
                </div>
            @endif
        </div>
    @endif

    <!-- Status Timeline -->
    <div class="booking-detail-section">
        <div class="booking-detail-title">📅 Timeline</div>
        
        <div class="timeline-item">
            <div class="timeline-date">Created</div>
            <div class="timeline-event">{{ $booking->created_at->format('M d, Y H:i') }}</div>
        </div>
        @if($booking->confirmed_at)
            <div class="timeline-item">
                <div class="timeline-date">Confirmed</div>
                <div class="timeline-event">{{ $booking->confirmed_at->format('M d, Y H:i') }}</div>
            </div>
        @endif
        @if($booking->cancelled_at)
            <div class="timeline-item">
                <div class="timeline-date">Cancelled</div>
                <div class="timeline-event">
                    {{ $booking->cancelled_at->format('M d, Y H:i') }}
                    @if($booking->cancellation_reason)
                        <div style="margin-top: 0.5rem; color: #ef5350; font-size: 0.9rem;">
                            <strong>Reason:</strong> {{ $booking->cancellation_reason }}
                        </div>
                    @endif
                </div>
            </div>
        @endif
        @if($booking->completed_at)
            <div class="timeline-item">
                <div class="timeline-date">Completed</div>
                <div class="timeline-event">{{ $booking->completed_at->format('M d, Y H:i') }}</div>
            </div>
        @endif
    </div>

    <!-- Notes -->
    @if($booking->internal_notes || $booking->admin_notes)
        <div class="booking-detail-section">
            <div class="booking-detail-title">📝 Notes</div>
            
            @if($booking->internal_notes)
                <div style="margin-bottom: 1rem;">
                    <div style="color: #8890a8; font-size: 0.9rem; margin-bottom: 0.5rem; text-transform: uppercase; letter-spacing: 0.08em;">Internal Notes</div>
                    <div class="notes-box">{{ $booking->internal_notes }}</div>
                </div>
            @endif

            @if($booking->admin_notes)
                <div>
                    <div style="color: #8890a8; font-size: 0.9rem; margin-bottom: 0.5rem; text-transform: uppercase; letter-spacing: 0.08em;">Admin Notes</div>
                    <div class="notes-box">{{ $booking->admin_notes }}</div>
                </div>
            @endif
        </div>
    @endif

    <!-- Action Buttons -->
    <div class="action-buttons">
        <a href="{{ route('dashboard') }}" class="btn-action btn-secondary">← Back to Dashboard</a>
        
        @if($booking->canBeCancelled() && auth()->id() === $booking->user_id)
            <form method="POST" action="{{ route('bookings.cancel', $booking) }}" style="display: inline;" onsubmit="return confirm('Are you sure you want to cancel this booking?');">
                @csrf
                <input type="hidden" name="confirm" value="1">
                <button type="submit" class="btn-action btn-danger">Cancel Booking</button>
            </form>
        @endif

        @if(auth()->guard('admin')->check() || auth()->user()?->role === 'admin')
            @if($booking->isPending())
                <form method="POST" action="{{ route('bookings.confirm', $booking) }}" style="display: inline;">
                    @csrf
                    <button type="submit" class="btn-action btn-primary">Confirm Booking</button>
                </form>
            @endif
        @endif

        <a href="{{ route('bookings.export', $booking) }}" class="btn-action btn-secondary">📄 Download</a>
    </div>
</x-layout>
