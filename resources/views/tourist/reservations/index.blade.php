<x-layout title="My Reservations">

<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="fw-semibold mb-0">My Reservations</h4>
    <a href="{{ route('packages.index') }}" class="btn btn-sm btn-primary">+ Book New Tour</a>
</div>

@if($bookings->isEmpty())
    <div class="card text-center py-5 text-muted">
        <p>You have no reservations yet.</p>
        <a href="{{ route('packages.index') }}" class="btn btn-primary btn-sm mx-auto" style="width:fit-content">
            Browse Packages
        </a>
    </div>
@else
    <div class="card">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Booking #</th>
                        <th>Package</th>
                        <th>Tour Date</th>
                        <th>Guests</th>
                        <th>Total</th>
                        <th>Status</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($bookings as $booking)
                    <tr>
                        <td class="font-monospace text-muted">#{{ $booking->booking_number }}</td>
                        <td>{{ $booking->tourPackage->name }}</td>
                        <td>{{ $booking->tour_date->format('M d, Y') }}</td>
                        <td>{{ $booking->num_guests }}</td>
                        <td class="text-success fw-semibold">₱{{ number_format($booking->total_price, 2) }}</td>
                        <td>
                            <span class="badge rounded-pill text-white" style="background-color: {{ $booking->status_color }};">
                                {{ $booking->status_label }}
                            </span>
                        </td>
                        <td>
                            <a href="{{ route('reservations.show', $booking) }}"
                               class="btn btn-sm btn-outline-secondary">View</a>
                            @if($booking->isPending())
                                <a href="{{ route('reservations.edit', $booking) }}"
                                   class="btn btn-sm btn-outline-primary">Edit</a>
                            @endif
                            @if($booking->isPending() || $booking->status === 'confirmed')
                                <button type="button" class="btn btn-sm btn-outline-danger" onclick="showCancelModal({{ $booking->id }})">Cancel</button>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-3">{{ $bookings->links() }}</div>
@endif

<!-- Single Cancellation Modal -->
<div class="modal fade" id="cancelModalIndex" tabindex="-1" aria-hidden="true" style="display: none;">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Cancel Reservation</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="cancelFormIndex" action="{{ route('reservations.cancel', ['booking' => '__ID__']) }}" method="POST">
                @csrf
                @method('DELETE')
                <div class="modal-body">
                    <p class="mb-3">Please provide a reason for cancelling this reservation:</p>
                    <div class="mb-3">
                        <label for="cancellation_reason_index" class="form-label">Reason for cancellation <span class="text-danger">*</span></label>
                        <textarea class="form-control" id="cancellation_reason_index" name="cancellation_reason" rows="4" required placeholder="Please explain why you need to cancel this reservation..."></textarea>
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

<script>
function showCancelModal(bookingId) {
    const form = document.getElementById('cancelFormIndex');
    form.action = form.action.replace('__ID__', bookingId);
    const modal = new bootstrap.Modal(document.getElementById('cancelModalIndex'));
    modal.show();
}
</script>

</x-layout>
