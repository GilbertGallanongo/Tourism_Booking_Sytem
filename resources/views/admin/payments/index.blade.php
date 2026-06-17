<x-layout>
    <div class="section mb-4">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h1 class="title">Payments</h1>
                <p class="lead">Review and verify incoming payment records.</p>
            </div>
        </div>
    </div>

    @if($payments->isEmpty())
        <div class="card empty">No payments recorded yet.</div>
    @else
        <div class="card">
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Booking</th>
                            <th>Customer</th>
                            <th>Amount</th>
                            <th>Status</th>
                            <th>Proof</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($payments as $payment)
                            @php
                                $displayStatus = $payment->status === 'unpaid' && ($payment->proof || $payment->reference_number)
                                    ? 'For review'
                                    : ucfirst($payment->status);
                            @endphp
                            <tr>
                                <td>{{ $payment->booking->booking_number ?? 'N/A' }}</td>
                                <td>{{ $payment->booking->user->name ?? 'N/A' }}</td>
                                <td>₱{{ number_format((float) $payment->amount, 2) }}</td>
                                <td>{{ $displayStatus }}</td>
                                <td>
                                    @if($payment->proof_url)
                                        <a href="{{ $payment->proof_url }}" target="_blank" rel="noopener">View proof</a>
                                    @elseif($payment->proof)
                                        <span title="{{ $payment->proof }}">Missing file</span>
                                    @else
                                        None
                                    @endif
                                </td>
                                <td class="text-end">
                                    <a href="{{ route('admin.payments.edit', $payment) }}" class="btn btn-sm btn-outline-secondary">Review</a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        <div class="mt-4">{{ $payments->links() }}</div>
    @endif
</x-layout>
