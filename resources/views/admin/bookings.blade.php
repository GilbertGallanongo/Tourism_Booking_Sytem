<x-layout>
    <div class="section">
        <h1 class="title">Booking Requests</h1>
        <p class="lead">Manage all tourist booking requests. Approve or decline bookings here.</p>
    </div>

    <div class="card">
        <div class="stack">
            @php
                $statusLabels = [
                    'pending' => 'Pending approval',
                    'approved' => 'Confirmed',
                    'cancelled' => 'Cancelled',
                    'declined' => 'Declined',
                    'completed' => 'Completed',
                    'cancellation_pending' => 'Cancellation Pending',
                ];

                $statusClasses = [
                    'pending' => 'status-pending',
                    'approved' => 'status-approved',
                    'cancelled' => 'status-cancelled',
                    'declined' => 'status-declined',
                    'completed' => 'status-completed',
                    'cancellation_pending' => 'status-cancellation-pending',
                ];
            @endphp

            <style>
                .filter-container {
                    background: transparent;
                    padding: 0.25rem 0;
                    border-radius: 0.25rem;
                    margin-bottom: 0.5rem;
                    border: none;
                    display: flex;
                    justify-content: flex-end; /* align filter to right */
                }

                /* Inline label + select */
                .filter-grid {
                    display: flex;
                    align-items: center;
                    gap: 0.5rem;
                }

                .filter-group {
                    display: inline-flex;
                    align-items: center;
                    gap: 0.5rem;
                }

                .filter-group label {
                    font-weight: 600;
                    margin-bottom: 0.4rem;
                    font-size: 0.9rem;
                    color: #fff; /* filter label text white */
                }

                .filter-group input,
                .filter-group select {
                    padding: 0.6rem;
                    border: 1px solid rgba(255,255,255,0.15);
                    border-radius: 0.4rem;
                    font-size: 0.9rem;
                    font-family: inherit;
                    color: #fff; /* filter control text white */
                    background: rgba(255,255,255,0.04);
                }

                /* Option text is difficult to style on some browsers, but set explicitly */
                .filter-group select option {
                    color: #000;
                    background: #fff;
                }

                .filter-group input:focus,
                .filter-group select:focus {
                    outline: none;
                    border-color: #0066cc;
                    box-shadow: 0 0 0 2px rgba(0, 102, 204, 0.1);
                }

                /* buttons removed — filter is just label + select */

                /* Status badge styles */
                .status {
                    display: inline-block;
                    padding: 0.25rem 0.6rem;
                    border-radius: 0.4rem;
                    font-weight: 600;
                    font-size: 0.9rem;
                    color: #fff; /* status text white */
                }

                .status-pending { background: #f39c12; }
                .status-approved { background: #28a745; }
                .status-declined { background: #dc3545; }
                .status-cancelled { background: #6c757d; }
                .status-completed { background: #6f42c1; }
                .status-cancellation-pending { background: #fd7e14; }
            </style>

            <!-- Filter Form (status only, inline) -->
            <form method="GET" action="{{ route('admin.bookings.index') }}" class="filter-container">
                <div class="filter-grid">
                    <div class="filter-group">
                        <label for="status">Status</label>
                        <select name="status" id="status" onchange="this.form.submit()">
                            <option value="all" {{ request('status') === 'all' || !request()->filled('status') ? 'selected' : '' }}>All Statuses</option>
                            <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending Approval</option>
                            <option value="approved" {{ request('status') === 'approved' ? 'selected' : '' }}>Confirmed</option>
                            <option value="declined" {{ request('status') === 'declined' ? 'selected' : '' }}>Declined</option>
                            <option value="cancelled" {{ request('status') === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                            <option value="cancellation_pending" {{ request('status') === 'cancellation_pending' ? 'selected' : '' }}>Cancellation Pending</option>
                        </select>
                    </div>
                </div>
            </form>

            @forelse($bookings as $booking)
                <div class="mini booking-row">
                    <div class="booking-info">
                        <div>
                            <strong>{{ $booking->booking_number }}</strong>
                            <p class="lead">Tourist: {{ $booking->user?->name ?? 'Guest' }}</p>
                            <p class="lead">{{ $booking->package?->name }} | Tour Date: {{ $booking->tour_date?->format('Y-m-d') }}</p>
                            <p class="lead">
                                📅 Tour start: {{ $booking->tour_start_date?->format('M d, Y') ?? 'Not set' }} 
                                | Tour end: {{ $booking->tour_end_date?->format('M d, Y') ?? 'Not set' }}
                            </p>
                            <p class="lead">Guests: {{ $booking->num_guests }} | Price: PHP {{ number_format((float) $booking->total_price, 2) }}</p>
                            @if($booking->promoPackage)
                                <p class="lead" style="color: #ffc107;"><strong>🎉 Promo:</strong> {{ $booking->promoPackage->name }} ({{ $booking->promoPackage->discount_percentage }}% OFF)</p>
                            @endif
                            @if($booking->tourist_guide)
                                <p class="lead"><strong>Tour guide included</strong> (₱{{ number_format($booking->tourist_guide_fee, 2) }})</p>
                            @endif
                            @if($booking->special_requests)
                                <p class="lead"><strong>Requests:</strong> {{ $booking->special_requests }}</p>
                            @endif
                            @if($booking->services && count($booking->services) > 0)
                                <p class="lead"><strong>Extras:</strong> {{ implode(', ', array_map(function ($service) {
                                    if (is_array($service)) {
                                        return $service['name'] ?? ucfirst(str_replace('_', ' ', $service['key'] ?? ''));
                                    }

                                    return ucfirst(str_replace('_', ' ', $service));
                                }, $booking->services->toArray())) }}</p>
                            @endif
                        </div>
                    </div>

                    <div class="booking-meta">
                        <span class="status {{ $statusClasses[$booking->status] ?? '' }}">
                            {{ $statusLabels[$booking->status] ?? ucfirst($booking->status) }}
                        </span>

                        @if($booking->status === 'pending')
                            <div class="booking-actions">
                                <form method="POST" action="{{ route('admin.bookings.status', $booking) }}">
                                    @csrf
                                    @method('PATCH')
                                    <input type="hidden" name="status" value="approved">
                                    <button type="submit" class="btn btn-primary">Confirm</button>
                                </form>

                                <form method="POST" action="{{ route('admin.bookings.status', $booking) }}">
                                    @csrf
                                    @method('PATCH')
                                    <input type="hidden" name="status" value="declined">
                                    <button type="submit" class="btn btn-outline-secondary">Decline</button>
                                </form>
                            </div>
                        @elseif($booking->status === 'approved')
                            <div class="booking-approved-info">
                                <p class="lead">Approved by {{ $booking->approver?->name ?? 'Admin' }}</p>
                                <p class="lead">{{ $booking->approved_at?->format('Y-m-d H:i') }}</p>
                            </div>
                        @elseif($booking->status === 'declined')
                            <div class="booking-declined-info">
                                <p class="lead">Declined by {{ $booking->approver?->name ?? 'Admin' }}</p>
                                <p class="lead">{{ $booking->approved_at?->format('Y-m-d H:i') }}</p>
                            </div>
                        @elseif($booking->status === 'cancelled')
                            <div class="booking-cancelled-info">
                                <p class="lead">Cancelled</p>
                                <p class="lead">{{ $booking->cancelled_at?->format('Y-m-d H:i') }}</p>
                                @if($booking->cancellation_reason)
                                    <button type="button" class="btn btn-info" onclick="showCancellationModal({{ $booking->id }}, '{{ $booking->cancellation_reason }}')">View Cancellation Reason</button>
                                @endif
                            </div>
                        @elseif($booking->status === 'cancellation_pending')
                            <div class="booking-cancellation-pending-info">
                                <p class="lead">{{ $booking->cancelled_at?->format('Y-m-d H:i') }}</p>
                                <div class="booking-actions">
                                    <button type="button" class="btn btn-info" onclick="showCancellationModal({{ $booking->id }}, '{{ $booking->cancellation_reason }}')">View Cancellation</button>
                                    <form method="POST" action="{{ route('admin.bookings.approve-cancellation', $booking) }}">
                                        @csrf
                                        <button type="submit" class="btn btn-danger">Approve Cancellation</button>
                                    </form>
                                    <form method="POST" action="{{ route('admin.bookings.reject-cancellation', $booking) }}">
                                        @csrf
                                        <button type="submit" class="btn btn-outline-secondary">Reject Cancellation</button>
                                    </form>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            @empty
                <p class="lead">No bookings found.</p>
            @endforelse

            @if($bookings->hasPages())
                <div class="pagination-wrapper">
                    {{ $bookings->links() }}
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Cancellation Details Modal -->
<div class="modal fade" id="cancellationModal" tabindex="-1" aria-hidden="true" style="display: none;">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Cancellation Reason</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p id="cancellationReasonText"></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<script>
function showCancellationModal(bookingId, reason) {
    document.getElementById('cancellationReasonText').textContent = reason;
    const modal = new bootstrap.Modal(document.getElementById('cancellationModal'));
    modal.show();
}
</script>

</x-layout>
