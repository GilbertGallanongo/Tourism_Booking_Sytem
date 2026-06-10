<x-layout title="Reservation Details">

<section class="reservation-page">
    <div class="reservation-hero">
        <a href="{{ route('reservations.index') }}" class="reservation-back">Back to reservations</a>

        <div class="reservation-grid">
            <div class="reservation-media">
                @if($booking->tourPackage->image)
                    <img src="{{ $booking->tourPackage->image_url }}" alt="{{ $booking->tourPackage->name }}">
                @else
                    <div class="reservation-placeholder" aria-hidden="true">Bolinao</div>
                @endif
                <span class="reservation-badge">{{ $booking->tourPackage->location }}</span>
            </div>

            <aside class="reservation-summary">
                <p class="reservation-kicker">Reservation details</p>
                <h1>Reservation #{{ $booking->booking_number }}</h1>

                <p class="reservation-description">{{ $booking->tourPackage->name }}</p>

                <div class="reservation-meta">
                    {{ optional($booking->tour_date)->format('M d, Y') }} &middot; {{ $booking->tourPackage->duration_days }} day(s)
                </div>

                <div class="reservation-price">
                    <span>Status</span>
                    <strong>{{ $booking->status_label }}</strong>
                </div>

                <div class="reservation-price">
                    <span>Total amount</span>
                    <strong>₱{{ number_format($booking->total_price, 2) }}</strong>
                </div>

                <div class="reservation-actions">
                    <a href="{{ route('reservations.index') }}" class="reservation-button">Back to reservations</a>
                </div>
            </aside>
        </div>
    </div>

    <div class="reservation-content">
        <section class="reservation-panel">
            <div class="reservation-heading">
                <p>Guest information</p>
                <h2>Contact & travelers</h2>
            </div>

            <div class="reservation-stats">
                <div>
                    <span>Contact</span>
                    <strong>{{ $booking->guest_details['name'] ?? $booking->user->name }}</strong>
                    <span>{{ $booking->guest_details['email'] ?? $booking->user->email }}</span>
                    <span>{{ $booking->guest_details['phone'] ?? 'Not provided' }}</span>
                </div>
                <div>
                    <span>Guests</span>
                    <strong>{{ $booking->num_guests }} total</strong>
                    <span>Adults: {{ $booking->num_adults ?? 0 }}</span>
                    <span>Children: {{ $booking->num_children ?? 0 }}, Seniors: {{ $booking->num_seniors ?? 0 }}</span>
                </div>
                <div>
                    <span>Booking codes</span>
                    <strong>{{ $booking->reference_code ?? 'Pending' }}</strong>
                    <span>Confirmation: {{ $booking->confirmation_code ?? 'Pending' }}</span>
                </div>
            </div>
        </section>

        <section class="reservation-panel">
            <div class="reservation-heading">
                <p>Reservation summary</p>
                <h2>Booking overview</h2>
            </div>

            <div class="reservation-overview-grid">
                <div class="reservation-overview-card">
                    <span>Tour start date</span>
                    <strong>{{ optional($booking->tour_start_date ?: $booking->tour_date)->format('M d, Y') }}</strong>
                </div>
                <div class="reservation-overview-card">
                    <span>Tour end date</span>
                    <strong>{{ optional($booking->tour_end_date)->format('M d, Y') ?: 'TBD' }}</strong>
                </div>
                <div class="reservation-overview-card">
                    <span>Duration</span>
                    <strong>{{ $booking->tourPackage->duration_days }} day(s)</strong>
                </div>
                <div class="reservation-overview-card">
                    <span>Guests</span>
                    <strong>{{ $booking->num_guests }} total</strong>
                </div>
            </div>

            <div class="reservation-overview-details">
                <div>
                    <p class="reservation-overview-label">Special requests</p>
                    <p>{{ $booking->special_requests ?: 'None' }}</p>
                </div>
                <div>
                    <p class="reservation-overview-label">Time</p>
                    <p>
                        @if($booking->tourPackage?->time_start || $booking->tourPackage?->time_end)
                            {{ $booking->tourPackage->time_start_formatted }}
                            @if($booking->tourPackage->time_end)
                                &mdash; {{ $booking->tourPackage->time_end_formatted }}
                            @endif
                        @else
                            {{ $booking->tour_start_date?->format('M d, Y') ?: 'TBD' }}
                        @endif
                    </p>
                </div>
            </div>
        </section>

        <section class="reservation-panel">
            <div class="reservation-heading">
                <p>Price breakdown</p>
                <h2>Trip cost</h2>
            </div>

            <div class="reservation-stats">
                <div>
                    <span>Package rate</span>
                    <strong>₱{{ number_format($booking->tourPackage->price, 2) }} x {{ $booking->num_guests }}</strong>
                </div>
                <div>
                    <span>Subtotal</span>
                    <strong>₱{{ number_format($booking->base_price ?? ($booking->tourPackage->price * $booking->num_guests), 2) }}</strong>
                </div>
                @if($booking->tourist_guide_fee > 0)
                    <div>
                        <span>Tour guide fee</span>
                        <strong>₱{{ number_format($booking->tourist_guide_fee, 2) }}</strong>
                    </div>
                @endif
                <div>
                    <span>Add-ons</span>
                    <strong>₱{{ number_format($booking->additional_fees ?? 0, 2) }}</strong>
                </div>
                <div>
                    <span>Total</span>
                    <strong>₱{{ number_format($booking->total_price, 2) }}</strong>
                </div>
            </div>
        </section>

        @if($booking->cancellation_reason)
            <section class="reservation-panel" style="border-color: {{ $booking->isCancellationPending() ? 'rgba(223, 183, 23, 0.45)' : 'rgba(220, 38, 38, 0.45)' }};">
                <div class="reservation-heading">
                    <p>{{ $booking->isCancellationPending() ? 'Cancellation request pending' : 'Cancellation details' }}</p>
                    <h2>{{ $booking->isCancellationPending() ? 'Awaiting review' : 'Reservation cancelled' }}</h2>
                </div>
                <p>{{ $booking->cancellation_reason }}</p>
                @if($booking->isCancelled())
                    <p class="mt-3"><strong>Refund</strong> ₱{{ number_format($booking->refund_amount ?? 0, 2) }}</p>
                @elseif($booking->isCancellationPending())
                    <p class="text-muted">Your cancellation request is awaiting admin approval.</p>
                @endif
            </section>
        @endif

        <section class="reservation-panel">
            <div class="reservation-heading">
                <p>Booking timeline</p>
                <h2>Next steps</h2>
            </div>

            <ul class="reservation-list">
                <li>
                    <strong>Request submitted</strong>
                    <p>Your request was received and is awaiting operator verification.</p>
                </li>
                <li>
                    <strong>Confirmation pending</strong>
                    <p>A local operator will confirm availability and payment details.</p>
                </li>
                <li>
                    <strong>Final payment</strong>
                    <p>Once confirmed, you will receive a payment invoice with a booking reference.</p>
                </li>
            </ul>
        </section>
    </div>

    <div class="d-flex flex-wrap justify-content-between gap-2 mt-3">
        <a href="{{ route('reservations.index') }}" class="reservation-button">Back to reservations</a>

        <div class="d-flex flex-wrap gap-2">
            @if($booking->canCheckIn())
                <form action="{{ route('reservations.check-in', $booking) }}" method="POST">
                    @csrf
                    <button type="submit" class="reservation-button reservation-button--success">Start Tour</button>
                </form>
            @endif

            @if($booking->canCheckOut())
                <form action="{{ route('reservations.check-out', $booking) }}" method="POST">
                    @csrf
                    <button type="submit" class="reservation-button reservation-button--primary">End Tour</button>
                </form>
            @endif

            @if($booking->canBeCancelled())
                <button type="button" class="reservation-button reservation-button--danger" data-bs-toggle="modal" data-bs-target="#cancelModal">Cancel Reservation</button>
            @elseif($booking->isCancellationPending())
                <button type="button" class="reservation-button reservation-button--warning" disabled>Cancellation Pending</button>
            @endif
        </div>
    </div>
</section>

@if($booking->canBeCancelled())
<!-- Cancellation Modal -->
<div class="modal fade" id="cancelModal" tabindex="-1" aria-labelledby="cancelModalLabel" aria-hidden="true" style="display: none;">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="cancelModalLabel">Cancel Reservation</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('reservations.cancel', $booking) }}" method="POST">
                @csrf
                @method('DELETE')
                <div class="modal-body">
                    <p class="mb-3">Please provide a reason for cancelling this reservation:</p>
                    <div class="mb-3">
                        <label for="cancellation_reason" class="form-label">Reason for cancellation <span class="text-danger">*</span></label>
                        <textarea class="form-control" id="cancellation_reason" name="cancellation_reason" rows="4" required placeholder="Please explain why you need to cancel this reservation..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-danger">Confirm Cancellation</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif

</x-layout>
