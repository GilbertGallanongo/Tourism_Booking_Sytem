<x-layout>
    <style>
        .proof-view-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            gap: 1rem;
            margin-bottom: 1.5rem;
            flex-wrap: wrap;
        }

        .proof-view-meta {
            color: var(--palette-secondary);
            margin: 0.35rem 0 0;
        }

        .proof-view-actions {
            display: flex;
            gap: 0.75rem;
            align-items: center;
            flex-wrap: wrap;
        }

        .proof-view-action {
            min-width: auto;
            white-space: nowrap;
        }

        .proof-view-back {
            background: rgba(114, 136, 174, 0.18) !important;
            border-color: rgba(114, 136, 174, 0.45) !important;
            color: var(--palette-cream) !important;
        }

        .proof-view-open {
            background: var(--palette-cream) !important;
            color: var(--palette-ink) !important;
        }

        .proof-view-action-note {
            flex-basis: 100%;
            color: rgba(234, 224, 207, 0.66);
            font-size: 0.82rem;
            margin: -0.25rem 0 0;
        }

        .proof-view-card {
            padding: 1rem;
            background: rgba(17, 24, 68, 0.38);
            border: 1px solid rgba(114, 136, 174, 0.35);
            border-radius: 0.75rem;
        }

        .proof-view-image {
            display: block;
            width: 100%;
            max-height: 78vh;
            object-fit: contain;
            border-radius: 0.5rem;
            background: rgba(0, 0, 0, 0.25);
        }

        .proof-view-empty {
            padding: 2rem;
            border: 1px dashed rgba(114, 136, 174, 0.35);
            border-radius: 0.75rem;
            color: var(--palette-secondary);
            text-align: center;
        }
    </style>

    @php
        $bookingNumber = $booking?->booking_number ?? $payment->booking?->booking_number ?? 'N/A';
        $customerName = $payment->booking?->user?->name ?? 'N/A';
    @endphp

    <div class="proof-view-header">
        <div>
            <h1 class="title">Proof of Payment</h1>
            <p class="proof-view-meta">
                Booking #{{ $bookingNumber }} &middot; {{ $customerName }} &middot; {{ $payment->proof_display_name }}
            </p>
        </div>

        <div class="proof-view-actions">
            <a href="{{ $backUrl }}" class="btn btn-outline-secondary proof-view-action proof-view-back" title="{{ $backLabel ?? 'Back' }}">
                &larr; Back
            </a>
            @if($payment->proof_url)
                <a href="{{ $payment->proof_url }}" class="btn btn-primary proof-view-action proof-view-open" target="_blank" rel="noopener">
                    {{ $payment->proof_is_image ? 'View proof image' : 'Open proof file' }}
                </a>
                <p class="proof-view-action-note">The proof opens in a new tab.</p>
            @endif
        </div>
    </div>

    <div class="proof-view-card">
        @if($payment->proof_url && $payment->proof_is_image)
            <img src="{{ $payment->proof_url }}" class="proof-view-image" alt="Proof of payment for {{ $bookingNumber }}">
        @elseif($payment->proof_url)
            <div class="proof-view-empty">
                Proof is attached, but it is not an image preview. Use Open proof file to view it.
            </div>
        @else
            <div class="proof-view-empty">
                No proof of payment file is available.
            </div>
        @endif
    </div>
</x-layout>
