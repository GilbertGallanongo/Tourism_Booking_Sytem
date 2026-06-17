<x-layout title="Reservation Details">

<style>
body.page-shell {
    background: #030712;
}

body.page-shell .content:has(.reservation-page) {
    max-width: none;
    padding: 0 1rem 2.5rem;
}

.reservation-page {
    position: relative;
    isolation: isolate;
    max-width: 58rem;
    margin: 0 auto;
    padding: 1.75rem 0 2rem;
    color: #f8fafc;
}

.reservation-page::before {
    content: "";
    position: fixed;
    inset: 0;
    z-index: -2;
    background:
        linear-gradient(90deg, rgba(2, 6, 23, 0.62), rgba(2, 6, 23, 0.28), rgba(2, 6, 23, 0.62)),
        url("/images/bolinao-church.jpg") center / cover no-repeat;
}

.reservation-page::after {
    content: "";
    position: fixed;
    inset: 0;
    z-index: -1;
    background: rgba(2, 6, 23, 0.22);
    pointer-events: none;
}

.reservation-hero,
.reservation-panel {
    background: rgba(8, 18, 44, 0.84);
    border: 1px solid rgba(148, 163, 184, 0.26);
    border-radius: 0.75rem;
    box-shadow: 0 1.25rem 3rem rgba(0, 0, 0, 0.34);
    backdrop-filter: blur(9px);
}

.reservation-hero {
    padding: 0.8rem;
}

.reservation-back {
    display: inline-flex;
    align-items: center;
    margin-bottom: 0.55rem;
    color: #e5e7eb;
    font-size: 0.72rem;
    font-weight: 800;
    text-decoration: none;
}

.reservation-grid {
    display: grid;
    grid-template-columns: minmax(0, 2fr) minmax(16rem, 0.8fr);
    gap: 0.8rem;
    align-items: stretch;
}

.reservation-media {
    position: relative;
    min-height: 16.5rem;
    overflow: hidden;
    border-radius: 0.6rem;
    background: rgba(15, 23, 42, 0.8);
}

.reservation-media img {
    width: 100%;
    height: 100%;
    min-height: 16.5rem;
    display: block;
    object-fit: cover;
}

.reservation-placeholder {
    min-height: 16.5rem;
    display: grid;
    place-items: center;
    color: #cbd5e1;
    font-size: 1.2rem;
    font-weight: 900;
    background: linear-gradient(135deg, #0f172a, #1e3a8a);
}

.reservation-badge {
    position: absolute;
    top: 0.75rem;
    right: 0.75rem;
    padding: 0.38rem 0.7rem;
    border-radius: 999px;
    color: #f8fafc;
    background: rgba(15, 23, 42, 0.72);
    font-size: 0.65rem;
    font-weight: 900;
}

.reservation-summary {
    display: flex;
    flex-direction: column;
    gap: 0.75rem;
    padding: 1rem;
    border: 1px solid rgba(148, 163, 184, 0.18);
    border-radius: 0.6rem;
    background: rgba(9, 20, 48, 0.72);
}

.reservation-kicker,
.reservation-heading p,
.reservation-overview-label,
.reservation-stats span,
.reservation-price span,
.reservation-overview-card span {
    margin: 0;
    color: #a7b2c7;
    font-size: 0.66rem;
    font-weight: 900;
    letter-spacing: 0;
    text-transform: uppercase;
}

.reservation-summary h1,
.reservation-heading h2 {
    margin: 0;
    color: #ffffff;
    line-height: 1.05;
}

.reservation-summary h1 {
    font-size: 1.35rem;
    overflow-wrap: anywhere;
}

.reservation-description,
.reservation-meta,
.reservation-overview-details p,
.reservation-list p {
    margin: 0;
    color: #e5e7eb;
}

.reservation-meta {
    font-size: 0.78rem;
    font-weight: 700;
}

.reservation-price,
.reservation-stats div,
.reservation-overview-card,
.reservation-overview-details div,
.reservation-list li {
    border: 1px solid rgba(148, 163, 184, 0.18);
    border-radius: 0.5rem;
    background: rgba(15, 23, 42, 0.42);
    padding: 0.75rem;
}

.reservation-price strong,
.reservation-stats strong,
.reservation-overview-card strong {
    display: block;
    color: #ffffff;
    font-size: 0.92rem;
    line-height: 1.25;
}

.reservation-content {
    display: grid;
    gap: 0.75rem;
    margin-top: 0.75rem;
}

.reservation-panel {
    padding: 0.95rem;
}

.reservation-heading {
    margin-bottom: 0.65rem;
}

.reservation-heading h2 {
    font-size: 1.22rem;
}

.reservation-stats {
    display: grid;
    gap: 0.55rem;
}

.reservation-contact-lines {
    display: grid;
    gap: 0.38rem;
    margin-top: 0.45rem;
}

.reservation-contact-line {
    display: grid;
    grid-template-columns: 4rem minmax(0, 1fr);
    gap: 0.6rem;
    align-items: baseline;
}

.reservation-contact-line span {
    color: #a7b2c7;
    font-size: 0.7rem;
    font-weight: 900;
    text-transform: uppercase;
}

.reservation-contact-line strong {
    min-width: 0;
    color: #ffffff;
    overflow-wrap: anywhere;
}

.reservation-overview-grid {
    display: grid;
    grid-template-columns: repeat(4, minmax(0, 1fr));
    gap: 0.55rem;
}

.reservation-overview-details {
    display: grid;
    gap: 0.55rem;
    margin-top: 0.55rem;
}

.reservation-list {
    display: grid;
    gap: 0.55rem;
    padding: 0;
    margin: 0;
    list-style: none;
}

.reservation-list strong {
    display: block;
    color: #ffffff;
    margin-bottom: 0.18rem;
}

.reservation-payment-grid {
    display: grid;
    grid-template-columns: minmax(0, 0.9fr) minmax(0, 1.1fr);
    gap: 0.7rem;
}

.reservation-payment-card {
    border: 1px solid rgba(148, 163, 184, 0.18);
    border-radius: 0.5rem;
    background: rgba(15, 23, 42, 0.42);
    padding: 0.8rem;
}

.reservation-payment-card h3 {
    margin: 0 0 0.6rem;
    color: #ffffff;
    font-size: 0.98rem;
    font-weight: 900;
}

.reservation-payment-row {
    display: flex;
    justify-content: space-between;
    gap: 0.8rem;
    padding: 0.45rem 0;
    border-bottom: 1px solid rgba(148, 163, 184, 0.14);
    color: #e5e7eb;
    font-size: 0.8rem;
}

.reservation-payment-row:last-child {
    border-bottom: 0;
}

.reservation-payment-row span {
    color: #a7b2c7;
    font-weight: 800;
}

.reservation-payment-row strong {
    color: #ffffff;
    text-align: right;
}

.reservation-payment-form {
    display: grid;
    gap: 0.7rem;
}

.reservation-payment-form label {
    display: block;
    margin-bottom: 0.35rem;
    color: #ffffff;
    font-size: 0.78rem;
    font-weight: 900;
}

.reservation-payment-form .form-control {
    width: 100%;
    min-height: 2.45rem;
    padding: 0.6rem 0.75rem;
    border: 1px solid rgba(128, 151, 202, 0.72);
    border-radius: 0.45rem;
    color: #f8fafc;
    background: rgba(8, 15, 38, 0.75);
}

.reservation-payment-help,
.reservation-payment-error {
    margin: 0.25rem 0 0;
    font-size: 0.72rem;
}

.reservation-payment-help {
    color: #cbd5e1;
}

.reservation-payment-error {
    color: #fecaca;
}

.reservation-proof-preview {
    margin-top: 0.75rem;
}

.reservation-proof-preview img {
    width: 100%;
    max-height: 14rem;
    object-fit: contain;
    border: 1px solid rgba(148, 163, 184, 0.18);
    border-radius: 0.5rem;
    background: rgba(2, 6, 23, 0.45);
}

.reservation-button {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    min-height: 2.35rem;
    padding: 0.6rem 1rem;
    border: 0;
    border-radius: 0.48rem;
    color: #ffffff;
    background: #2563eb;
    font-size: 0.78rem;
    font-weight: 900;
    text-decoration: none;
    cursor: pointer;
}

.reservation-button:hover {
    color: #ffffff;
    filter: brightness(1.08);
}

.reservation-button--danger {
    background: #ef1d1d;
}

.reservation-button--success {
    background: #16a34a;
}

.reservation-button--primary {
    background: #2563eb;
}

.reservation-button--warning {
    background: #d97706;
    cursor: not-allowed;
}

.reservation-actions .reservation-button,
.reservation-bottom-actions .reservation-button,
.reservation-bottom-actions form {
    width: 100%;
}

.reservation-bottom-actions {
    display: grid;
    gap: 0.45rem;
    margin-top: 0.75rem;
}

@media (max-width: 760px) {
    body.page-shell .content:has(.reservation-page) {
        padding-inline: 0.7rem;
    }

    .reservation-grid,
    .reservation-overview-grid,
    .reservation-payment-grid {
        grid-template-columns: 1fr;
    }

    .reservation-media,
    .reservation-media img,
    .reservation-placeholder {
        min-height: 14rem;
    }

    .reservation-summary h1 {
        font-size: 1.15rem;
    }
}
</style>

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
                    <div class="reservation-contact-lines">
                        <div class="reservation-contact-line">
                            <span>Name</span>
                            <strong>{{ $booking->guest_details['name'] ?? $booking->guest_details['contact_name'] ?? $booking->user->name }}</strong>
                        </div>
                        <div class="reservation-contact-line">
                            <span>Email</span>
                            <strong>{{ $booking->guest_details['email'] ?? $booking->guest_details['contact_email'] ?? $booking->user->email }}</strong>
                        </div>
                        <div class="reservation-contact-line">
                            <span>Phone</span>
                            <strong>{{ $booking->guest_details['phone'] ?? $booking->guest_details['contact_phone'] ?? 'Not provided' }}</strong>
                        </div>
                    </div>
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

        @php
            $payment = $booking->payment;
            $paymentStatus = $payment?->status ?? 'unpaid';
            $paymentSubmittedForReview = $paymentStatus === 'unpaid' && ($payment?->has_uploaded_proof || $payment?->reference_number);
            $paymentDisplayStatus = $paymentStatus === 'unpaid' && ($payment?->has_uploaded_proof || $payment?->reference_number)
                ? 'Submitted for review'
                : ucfirst($paymentStatus);
            $tourHasEnded = $booking->isCompleted() || (bool) $booking->tour_ended_at;
            $canSubmitPayment = ($booking->isConfirmed() || $tourHasEnded)
                && ! $booking->isCancelled()
                && $paymentStatus !== 'paid'
                && ! $paymentSubmittedForReview;
            $paymentMethodLabel = $payment?->method
                ? ucwords(str_replace('_', ' ', $payment->method))
                : 'Not selected';

            if ($paymentStatus === 'unpaid' && $payment?->method === 'cash') {
                $paymentMethodLabel = 'Not selected';
            }
        @endphp

        <section class="reservation-panel">
            <div class="reservation-heading">
                <p>Payment</p>
                <h2>Settle your reservation</h2>
            </div>

            <div class="reservation-payment-grid">
                <div class="reservation-payment-card">
                    <h3>Payment status</h3>
                    <div class="reservation-payment-row">
                        <span>Amount</span>
                        <strong>PHP {{ number_format((float) ($payment?->amount ?? $booking->total_price), 2) }}</strong>
                    </div>
                    <div class="reservation-payment-row">
                        <span>Status</span>
                        <strong>{{ $paymentDisplayStatus }}</strong>
                    </div>
                    <div class="reservation-payment-row">
                        <span>Method</span>
                        <strong>{{ $paymentMethodLabel }}</strong>
                    </div>
                    <div class="reservation-payment-row">
                        <span>Reference</span>
                        <strong>{{ $payment?->reference_number ?: 'Not provided' }}</strong>
                    </div>

                    @if($payment?->proof_url)
                        <div class="reservation-proof-preview">
                            @if($payment->proof_is_image)
                                <a href="{{ route('reservations.proof', $booking) }}">
                                    <img src="{{ $payment->proof_url }}" alt="Proof of payment for {{ $booking->booking_number }}">
                                </a>
                            @else
                                <a href="{{ route('reservations.proof', $booking) }}" class="reservation-button">Open proof of payment</a>
                            @endif
                        </div>
                    @endif
                </div>

                <div class="reservation-payment-card">
                    @if($paymentStatus === 'paid')
                        <h3>Payment verified</h3>
                        <p class="reservation-payment-help">Your payment has been approved by the admin.</p>
                    @elseif($paymentSubmittedForReview)
                        <h3>Submitted for review</h3>
                        <p class="reservation-payment-help">Your payment details have been submitted. Please wait for admin verification before making any changes.</p>
                    @elseif($booking->isCancelled())
                        <h3>Reservation cancelled</h3>
                        <p class="reservation-payment-help">Payment submission is not available for cancelled reservations.</p>
                    @elseif(! $booking->isConfirmed() && ! $tourHasEnded)
                        <h3>Waiting for confirmation</h3>
                        <p class="reservation-payment-help">Your reservation is still under admin review. Payment submission will open after the booking is approved.</p>
                    @elseif($canSubmitPayment)
                        <h3>Submit payment details</h3>
                        @if($tourHasEnded)
                            <p class="reservation-payment-help">This tour has ended, but the balance is still unpaid. You can still submit payment details for admin review.</p>
                        @endif
                        <form action="{{ route('reservations.payment', $booking) }}" method="POST" enctype="multipart/form-data" class="reservation-payment-form">
                            @csrf

                            <div>
                                <label for="payment_method">Payment method</label>
                                <select id="payment_method" name="method" class="form-control" required>
                                    <option value="gcash" @selected(old('method', $payment?->method ?? 'gcash') === 'gcash')>GCash</option>
                                    <option value="bank_transfer" @selected(old('method', $payment?->method) === 'bank_transfer')>Bank transfer</option>
                                    <option value="cash" @selected(old('method', $payment?->method) === 'cash')>Cash on arrival</option>
                                </select>
                                @error('method')
                                    <p class="reservation-payment-error">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="reference_number">Reference number</label>
                                <input id="reference_number" name="reference_number" type="text" value="{{ old('reference_number', $payment?->reference_number) }}" class="form-control" placeholder="Enter GCash or bank reference number">
                                @error('reference_number')
                                    <p class="reservation-payment-error">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="proof">Proof of payment</label>
                                <input id="proof" name="proof" type="file" accept="image/jpeg,image/png,image/webp,application/pdf" class="form-control">
                                <p class="reservation-payment-help">Upload a screenshot or PDF receipt. Required for GCash and bank transfer. Max 5MB.</p>
                                @error('proof')
                                    <p class="reservation-payment-error">{{ $message }}</p>
                                @enderror
                            </div>

                            <button type="submit" class="reservation-button">Submit payment for review</button>
                        </form>
                    @elseif($tourHasEnded)
                        <h3>Tour ended</h3>
                        <p class="reservation-payment-help">This tour has already ended and no unpaid balance is available for submission.</p>
                    @else
                        <h3>Payment unavailable</h3>
                        <p class="reservation-payment-help">Payment submission is not available for this reservation status.</p>
                    @endif
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
                    <p>Your reservation request was received and is awaiting review.</p>
                </li>
                <li>
                    <strong>Admin confirmation</strong>
                    <p>The admin will review availability and approve the reservation if the schedule can be accommodated.</p>
                </li>
                <li>
                    <strong>Submit payment details</strong>
                    <p>After approval, choose a payment method, enter your reference number, and upload your proof of payment.</p>
                </li>
            </ul>
        </section>
    </div>

    <div class="reservation-bottom-actions">
        <a href="{{ route('reservations.index') }}" class="reservation-button">Back to reservations</a>

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
