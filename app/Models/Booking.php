<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use App\Models\Review;

class Booking extends Model
{
    use HasFactory;

    protected $fillable = [
        'booking_number',
        'confirmation_code',
        'reference_code',
        'user_id',
        'tour_package_id',
        'promo_package_id',
        'tour_date',
        'tour_start_date',
        'tour_end_date',
        'num_guests',
        'num_adults',
        'num_children',
        'num_seniors',
        'status',
        'total_price',
        'base_price',
        'additional_fees',
        'tourist_guide',
        'tourist_guide_fee',
        'discount_amount',
        'discount_code',
        'special_requests',
        'cancellation_reason',
        'refund_amount',
        'cancelled_at',
        'approved_by',
        'approved_at',
        'confirmed_at',
        'completed_at',
        'tour_started_at',
        'tour_ended_at',
        'payment_plan',
        'payment_installments',
        'guest_details',
        'services',
        'internal_notes',
        'admin_notes',
        'reminder_sent',
        'reminder_sent_at',
    ];

    protected function casts(): array
    {
        return [
            'tour_date' => 'date',
            'approved_at' => 'datetime',
            'cancelled_at' => 'datetime',
            'confirmed_at' => 'datetime',
            'completed_at' => 'datetime',
            'tour_start_date' => 'date',
            'tour_end_date' => 'date',
            'tour_started_at' => 'datetime',
            'tour_ended_at' => 'datetime',
            'reminder_sent_at' => 'datetime',
            'num_guests' => 'integer',
            'num_adults' => 'integer',
            'num_children' => 'integer',
            'num_seniors' => 'integer',
            'total_price' => 'decimal:2',
            'base_price' => 'decimal:2',
            'additional_fees' => 'decimal:2',
            'tourist_guide' => 'boolean',
            'tourist_guide_fee' => 'decimal:2',
            'discount_amount' => 'decimal:2',
            'refund_amount' => 'decimal:2',
            'payment_installments' => 'integer',
            'reminder_sent' => 'boolean',
            'guest_details' => 'collection',
            'services' => 'collection',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function package(): BelongsTo
    {
        return $this->belongsTo(TourPackage::class, 'tour_package_id');
    }

    // Backwards-compatible alias for existing relationship name used across views/controllers
    public function tourPackage(): BelongsTo
    {
        return $this->package();
    }

    public function payment(): HasOne
    {
        return $this->hasOne(Payment::class);
    }

    public function promoPackage(): BelongsTo
    {
        return $this->belongsTo(PromoPackage::class);
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(Admin::class, 'approved_by');
    }

    // Lazy accessor: the review left by the booking user for this booked tour package
    public function getReviewAttribute()
    {
        return Review::where('tour_package_id', $this->tour_package_id)
            ->where('user_id', $this->user_id)
            ->first();
    }

    // Helper Methods
    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function isConfirmed(): bool
    {
        return $this->status === 'confirmed' || $this->status === 'approved';
    }

    public function isCancelled(): bool
    {
        return $this->status === 'cancelled';
    }

    public function isCancellationPending(): bool
    {
        return $this->status === 'cancellation_pending';
    }

    public function isCompleted(): bool
    {
        return $this->status === 'completed';
    }

    public function canBeCancelled(): bool
    {
        return in_array($this->status, ['pending', 'confirmed', 'approved'])
            && $this->tour_date->isFuture()
            && !$this->isCancellationPending();
    }

    public function canCheckIn(): bool
    {
        return $this->isConfirmed()
            && !$this->tour_started_at
            && !$this->tour_ended_at
            && !$this->isCancelled()
            && !$this->tour_date->isFuture();
    }

    public function canCheckOut(): bool
    {
        return $this->tour_started_at
            && !$this->tour_ended_at
            && !$this->isCancelled();
    }

    public function markAsCheckedIn(): void
    {
        $this->update(['tour_started_at' => now()]);
    }

    public function markAsCheckedOut(): void
    {
        $this->update([
            'tour_ended_at' => now(),
            'status' => 'completed',
            'completed_at' => now(),
        ]);
    }

    public function generateConfirmationCode(): string
    {
        return 'CONF-' . strtoupper(\Illuminate\Support\Str::random(10));
    }

    public function generateReferenceCode(): string
    {
        return 'REF-' . now()->format('Ymd') . '-' . strtoupper(\Illuminate\Support\Str::random(6));
    }

    public function calculateTotalPrice(): float
    {
        $base = $this->base_price ?? $this->package->price * $this->num_guests;
        $guideFee = $this->tourist_guide_fee ?? 0;
        return max(0, ($base + $this->additional_fees + $guideFee) - $this->discount_amount);
    }

    public function markAsConfirmed(): void
    {
        $this->update([
            'status' => 'confirmed',
            'confirmed_at' => now(),
            'confirmation_code' => $this->confirmation_code ?? $this->generateConfirmationCode(),
            'reference_code' => $this->reference_code ?? $this->generateReferenceCode(),
        ]);
    }

    public function markAsCancelled(string $reason = '', float $refundAmount = null): void
    {
        $this->update([
            'status' => 'cancelled',
            'cancelled_at' => now(),
            'cancellation_reason' => $reason,
            'refund_amount' => $refundAmount ?? $this->total_price,
        ]);
    }

    public function markAsCompleted(): void
    {
        $this->update([
            'status' => 'completed',
            'completed_at' => now(),
        ]);
    }

    public function getRemainingDaysAttribute(): ?int
    {
        if (!$this->tour_date) {
            return null;
        }
        return now()->diffInDays($this->tour_date, false);
    }

    public function getStatusColorAttribute(): string
    {
        if ($this->tour_ended_at) {
            return '#1976d2';
        }

        if ($this->tour_started_at) {
            return '#4caf50';
        }

        return match($this->status) {
            'pending' => '#ffc107',
            'confirmed', 'approved' => '#81c784',
            'declined' => '#dc3545',
            'cancelled' => '#6c757d',
            'cancellation_pending' => '#fd7e14',
            'completed' => '#64b5f6',
            default => '#9e9e9e',
        };
    }

    public function getStatusLabelAttribute(): string
    {
        if ($this->tour_ended_at) {
            return 'Tour Ended';
        }

        if ($this->tour_started_at) {
            return 'Tour Started';
        }

        return match($this->status) {
            'cancellation_pending' => 'Cancellation Pending',
            default => ucfirst(str_replace('_', ' ', $this->status)),
        };
    }
}
