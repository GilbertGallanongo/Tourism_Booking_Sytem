<x-layout>
    <style>
        .payment-review-summary {
            display: flex;
            flex-wrap: wrap;
            gap: 1rem 1.25rem;
            margin-bottom: 1.5rem;
            color: var(--palette-secondary);
        }

        .payment-review-summary span {
            display: inline-flex;
            gap: 0.5rem;
            align-items: center;
        }

        .payment-review-summary strong {
            color: var(--palette-cream);
        }

        .proof-panel {
            border: 1px solid rgba(114, 136, 174, 0.35);
            border-radius: 0.75rem;
            overflow: hidden;
            background: rgba(17, 24, 68, 0.38);
        }

        .proof-panel__header {
            display: flex;
            justify-content: space-between;
            gap: 1rem;
            align-items: center;
            padding: 0.85rem 1rem;
            border-bottom: 1px solid rgba(114, 136, 174, 0.22);
        }

        .proof-panel__path {
            color: var(--palette-secondary);
            font-size: 0.875rem;
            word-break: break-all;
        }

        .proof-panel__body {
            padding: 1rem;
        }

        .proof-panel img {
            display: block;
            width: 100%;
            max-height: 520px;
            object-fit: contain;
            border-radius: 0.5rem;
            background: rgba(0, 0, 0, 0.2);
        }

        .proof-empty {
            padding: 1rem;
            color: var(--palette-secondary);
            border: 1px dashed rgba(114, 136, 174, 0.35);
            border-radius: 0.75rem;
        }

        .payment-actions {
            display: flex;
            gap: 0.75rem;
            align-items: center;
            flex-wrap: wrap;
        }

        .payment-actions .btn {
            width: auto;
            min-width: 120px;
        }
    </style>

    <div class="section">
        <h1 class="title">Review Payment</h1>
        <p class="lead">Approve or update this payment record.</p>
    </div>

    <div class="card">
        <div class="card-body">
            <div class="payment-review-summary">
                <span>Booking number <strong>{{ $payment->booking->booking_number }}</strong></span>
                <span>Customer <strong>{{ $payment->booking->user->name }}</strong></span>
                <span>Amount <strong>PHP {{ number_format((float) $payment->amount, 2) }}</strong></span>
                <span>Current status <strong>{{ ucfirst($payment->status) }}</strong></span>
            </div>

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
                    @if($payment->proof_url)
                        <div class="proof-panel">
                            <div class="proof-panel__header">
                                <div class="proof-panel__path">{{ $payment->proof_display_name }}</div>
                                <a href="{{ route('admin.payments.proof', $payment) }}" class="btn btn-sm btn-outline-secondary">Open proof</a>
                            </div>
                            <div class="proof-panel__body">
                                @if($payment->proof_url && $payment->proof_is_image)
                                    <a href="{{ route('admin.payments.proof', $payment) }}">
                                        <img src="{{ $payment->proof_url }}" alt="Proof of payment for {{ $payment->booking->booking_number }}">
                                    </a>
                                @elseif($payment->proof_url)
                                    <div class="proof-empty">
                                        Proof is attached, but it is not an image preview. Use Open proof to view it.
                                    </div>
                                @else
                                    <div class="proof-empty">
                                        Proof path is saved, but the file cannot be found. Check that the file exists in public or storage.
                                    </div>
                                @endif
                            </div>
                        </div>
                    @else
                        <div class="proof-empty">No proof of payment attached.</div>
                    @endif
                </div>

                <div class="payment-actions">
                    <button type="submit" class="btn btn-primary">Save Payment</button>
                    <a href="{{ route('admin.payments.index') }}" class="btn btn-outline-secondary">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</x-layout>
