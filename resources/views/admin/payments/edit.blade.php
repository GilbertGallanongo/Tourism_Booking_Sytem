<x-layout>
    <div class="section">
        <h1 class="title">Review Payment</h1>
        <p class="lead">Approve or update this payment record.</p>
    </div>

    <div class="card">
        <div class="card-body">
            <dl class="row mb-4">
                <dt class="col-4 text-muted">Booking number</dt>
                <dd class="col-8">{{ $payment->booking->booking_number }}</dd>

                <dt class="col-4 text-muted">Customer</dt>
                <dd class="col-8">{{ $payment->booking->user->name }}</dd>

                <dt class="col-4 text-muted">Amount</dt>
                <dd class="col-8">₱{{ number_format((float) $payment->amount, 2) }}</dd>

                <dt class="col-4 text-muted">Current status</dt>
                <dd class="col-8">{{ ucfirst($payment->status) }}</dd>
            </dl>

            <form action="{{ route('admin.payments.update', $payment) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="mb-3">
                    <label class="form-label">Status</label>
                    <select name="status" class="form-control">
                        @foreach(['unpaid', 'paid', 'refunded'] as $status)
                            <option value="{{ $status }}" {{ old('status', $payment->status) === $status ? 'selected' : '' }}>{{ ucfirst($status) }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label">Reference number</label>
                    <input type="text" name="reference_number" value="{{ old('reference_number', $payment->reference_number) }}" class="form-control">
                </div>

                <div class="mb-3">
                    <label class="form-label">Proof of payment</label>
                    <input type="text" name="proof" value="{{ old('proof', $payment->proof) }}" class="form-control" placeholder="images/proof.jpg">
                </div>

                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary">Save Payment</button>
                    <a href="{{ route('admin.payments.index') }}" class="btn btn-outline-secondary">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</x-layout>
