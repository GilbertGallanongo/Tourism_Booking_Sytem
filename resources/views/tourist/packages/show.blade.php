<x-layout :title="$tourPackage->name">
    <style>
        /* Package detail hero layout */
        .package-detail-page { padding: 2rem 1.25rem; }
        .package-detail-hero { max-width: 1100px; margin: 0 auto; }
        .package-detail-grid { display: grid; grid-template-columns: 1fr 360px; gap: 1.5rem; align-items: start; }
        .package-detail-media img { width: 100%; height: 360px; object-fit: cover; border-radius: 12px; box-shadow: 0 6px 30px rgba(0,0,0,0.6); }
        .package-detail-media { position: relative; }
        .package-detail-badge { position: absolute; right: 1rem; top: 1rem; background: rgba(17,31,77,0.9); color: #fff; padding: 0.35rem 0.6rem; border-radius: 8px; font-size: 0.85rem; }
        .package-detail-summary { background: rgba(6,10,23,0.85); color: #fff; border-radius: 12px; padding: 1.25rem; box-shadow: 0 10px 40px rgba(0,0,0,0.6); }
        .package-detail-kicker { font-size: 0.85rem; color: #cbd5e1; margin-bottom: 0.4rem; }
        .package-detail-rating span { color: #ffd54d; margin-right: 2px; }
        .package-detail-location { color: rgba(234,224,207,0.8); margin: 0.5rem 0; }
        .package-detail-description { color: rgba(234,224,207,0.85); margin-bottom: 0.75rem; }
        .package-detail-price { background: rgba(10,14,30,0.6); padding: 0.8rem; border-radius: 8px; text-align: center; margin-top: 1rem; }
        .package-detail-price strong { display:block; font-size:1.35rem; margin-top:0.35rem; }
        .package-detail-primary { display:inline-block; margin-top:0.75rem; padding:0.6rem 1rem; border-radius:8px; background:#3b82f6; color:#fff; text-decoration:none; }

        /* Panels */
        .package-detail-content { max-width: 1100px; margin: 1.5rem auto 4rem; }
        .package-detail-panel { background: rgba(8,12,22,0.7); padding: 1rem; border-radius: 10px; margin-bottom: 1rem; color: #eae0cf; box-shadow: 0 6px 30px rgba(0,0,0,0.55); }
        .package-detail-stats { display:grid; grid-template-columns: repeat(4,1fr); gap:1rem; }
        .package-detail-stats div { background: rgba(10,16,32,0.5); padding:0.75rem; border-radius:8px; text-align:center; }
        .package-detail-review-list { display:flex; flex-direction:column; gap:0.75rem; }
        .package-detail-review { background: rgba(2,6,14,0.55); padding:0.75rem; border-radius:8px; }
        .package-detail-review-actions { display:flex; flex-wrap:wrap; gap:0.5rem; margin-top:0.75rem; }
        .package-detail-edit { width:100%; }
        .package-detail-edit summary,
        .package-detail-delete-form button { cursor:pointer; border:0; border-radius:8px; padding:0.45rem 0.75rem; font-weight:800; color:#fff; background:#334155; }
        .package-detail-delete-form button { background:#dc2626; }
        .package-detail-edit-form { display:grid; gap:0.6rem; margin-top:0.75rem; }
        .package-detail-edit-form select,
        .package-detail-edit-form textarea { width:100%; border:1px solid rgba(148,163,184,0.35); border-radius:8px; padding:0.65rem; color:#fff; background:rgba(15,23,42,0.85); }
        .package-detail-review-lock { margin-top:0.75rem; color:#cbd5e1; font-size:0.85rem; }
        @media (max-width: 880px) {
            .package-detail-grid { grid-template-columns: 1fr; }
            .package-detail-media img { height: 260px; }
            .package-detail-stats { grid-template-columns: repeat(2,1fr); }
        }
    </style>
@php
    $selectedPromoId = request('promo');
@endphp
    <section class="package-detail-page">
        <div class="package-detail-hero">
            <a href="{{ route('packages.index', request()->only('promo')) }}" class="package-detail-back">Back to packages</a>

            <div class="package-detail-grid">
                <div class="package-detail-media">
                    @if($tourPackage->image)
                        <img src="{{ $tourPackage->image_url }}" alt="{{ $tourPackage->name }}">
                    @else
                        <div class="package-detail-placeholder" aria-hidden="true">Bolinao</div>
                    @endif
                    <span class="package-detail-badge">{{ $tourPackage->category_label }}</span>
                </div>

                <aside class="package-detail-summary">
                    <p class="package-detail-kicker">Tour package</p>
                    <h1>{{ $tourPackage->name }}</h1>

                    <div class="package-detail-rating" aria-label="Rated {{ number_format($tourPackage->average_rating, 1) }} out of 5">
                        @for($i = 1; $i <= 5; $i++)
                            <span>{!! $i <= round($tourPackage->average_rating) ? '&#9733;' : '&#9734;' !!}</span>
                        @endfor
                        <strong>{{ number_format($tourPackage->average_rating, 1) }}</strong>
                    </div>

                    <p class="package-detail-location">{{ $tourPackage->location }}</p>
                    <p class="package-detail-description">{{ $tourPackage->description }}</p>
                    <p class="package-detail-schedule">{{ $tourPackage->time_start_formatted }} &mdash; {{ $tourPackage->time_end_formatted }}</p>

                    <div class="package-detail-price">
                        <span>Price per person</span>
                        @if(isset($selectedPromo) && $selectedPromo?->isActive())
                            <span style="display:block; color: rgba(234, 224, 207, 0.75); font-size: 0.95rem;">{{ $selectedPromo->name }} • {{ number_format($selectedPromo->discount_percentage, 0) }}% OFF</span>
                            <strong>&#8369;{{ number_format($selectedPromo->discountedPrice($tourPackage->price), 2) }}</strong>
                            <span style="display:block; font-size: 0.88rem; color: rgba(255,255,255,0.7); text-decoration: line-through;">&#8369;{{ number_format($tourPackage->price, 2) }}</span>
                        @else
                            <strong>&#8369;{{ number_format($tourPackage->price, 2) }}</strong>
                        @endif
                    </div>

                    <div class="package-detail-actions">
                        @auth
                            @if(auth()->user()->isGuest())
                                <button type="button" class="package-detail-primary" data-auth-open data-auth-mode="register">
                                    Register to Book
                                </button>
                                <p>Guest accounts can browse tours only. Create a tourist account to make a booking.</p>
                            @elseif(auth()->user()->isTourist())
                                <a href="{{ route('bookings.create', array_merge([$tourPackage], request()->only('promo'))) }}" class="package-detail-primary">
                                    Reserve This Tour
                                </a>
                            @else
                                <a href="#" class="package-detail-primary" data-auth-open data-auth-mode="signin">
                                    Sign in as Tourist
                                </a>
                            @endif
                        @else
                            <a href="#" class="package-detail-primary" data-auth-open data-auth-mode="signin">
                                Login to Book
                            </a>
                        @endauth
                    </div>
                </aside>
            </div>
        </div>

        <div class="package-detail-content">
            <section class="package-detail-panel package-detail-overview">
                <div class="package-detail-section-heading">
                    <p>Trip overview</p>
                    <h2>What to expect</h2>
                </div>

                <div class="package-detail-stats">
                    <div>
                        <span>Destination</span>
                        <strong>{{ $tourPackage->destination?->name ?? 'Bolinao' }}</strong>
                    </div>
                    <div>
                        <span>Duration</span>
                        <strong>{{ $tourPackage->duration_days }} day{{ $tourPackage->duration_days === 1 ? '' : 's' }}</strong>
                    </div>
                    <div>
                        <span>Max guests</span>
                        <strong>{{ $tourPackage->max_guests }}</strong>
                    </div>
                    <div>
                        <span>Category</span>
                        <strong>{{ $tourPackage->category_label }}</strong>
                    </div>
                </div>
            </section>

            <section class="package-detail-panel package-detail-reviews">
                <div class="package-detail-section-heading">
                    <p>Traveler feedback</p>
                    <h2>Reviews</h2>
                </div>

                @if($tourPackage->reviews->isEmpty())
                    <div class="package-detail-empty">
                        No reviews yet. Be the first to share your experience.
                    </div>
                @else
                    <div class="package-detail-review-list">
                        @foreach($tourPackage->reviews as $review)
                            @php
                                $canModifyReview = $review->canBeModifiedBy(auth()->user());
                                $ownsReview = auth()->id() === $review->user_id;
                            @endphp
                            <article class="package-detail-review">
                                <div class="package-detail-review-top">
                                    <div>
                                        <strong>{{ $review->user->name }}</strong>
                                        <span>{{ $review->created_at->diffForHumans() }}</span>
                                    </div>
                                    <div class="package-detail-review-stars">
                                        @for($i = 1; $i <= 5; $i++)
                                            <span>{!! $i <= $review->rating ? '&#9733;' : '&#9734;' !!}</span>
                                        @endfor
                                    </div>
                                </div>
                                <p>{{ $review->comment }}</p>
                                @if($canModifyReview)
                                    <div class="package-detail-review-actions">
                                        <details class="package-detail-edit">
                                            <summary>Edit review</summary>
                                            <form action="{{ route('reviews.update', $review) }}" method="POST" class="package-detail-edit-form">
                                                @csrf
                                                @method('PATCH')
                                                <label>
                                                    <span>Rating</span>
                                                    <select name="rating">
                                                        @for($i = 1; $i <= 5; $i++)
                                                            <option value="{{ $i }}" {{ old('rating', $review->rating) == $i ? 'selected' : '' }}>{{ $i }} star{{ $i === 1 ? '' : 's' }}</option>
                                                        @endfor
                                                    </select>
                                                </label>
                                                <label>
                                                    <span>Comment</span>
                                                    <textarea name="comment" rows="4">{{ old('comment', $review->comment) }}</textarea>
                                                </label>
                                                <button type="submit" class="package-detail-primary package-detail-submit">Save changes</button>
                                            </form>
                                        </details>

                                        <form action="{{ route('reviews.destroy', $review) }}" method="POST" class="package-detail-delete-form" onsubmit="return confirm('Delete this review?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit">Delete review</button>
                                        </form>
                                    </div>
                                @elseif($ownsReview)
                                    <p class="package-detail-review-lock">Edit and delete are available only within 3 days after posting.</p>
                                @endif
                            </article>
                        @endforeach
                    </div>
                @endif
            </section>

            @if(auth()->check() && auth()->user()->isTourist() && ! auth()->user()->isGuest())
                <section class="package-detail-panel package-detail-review-form">
                    <div class="package-detail-section-heading">
                        <p>Your experience</p>
                        <h2>Submit a review</h2>
                    </div>

                    <form action="{{ route('reviews.store', $tourPackage) }}" method="POST">
                        @csrf
                        <div class="package-detail-form-grid">
                            <label>
                                <span>Rating</span>
                                <select name="rating">
                                    @for($i = 1; $i <= 5; $i++)
                                        <option value="{{ $i }}" {{ old('rating') == $i ? 'selected' : '' }}>{{ $i }} star{{ $i === 1 ? '' : 's' }}</option>
                                    @endfor
                                </select>
                            </label>
                            <label>
                                <span>Comment</span>
                                <textarea name="comment" rows="4">{{ old('comment') }}</textarea>
                            </label>
                        </div>
                        <button type="submit" class="package-detail-primary package-detail-submit">Submit Review</button>
                    </form>
                </section>
            @elseif(auth()->check())
                <div class="package-detail-note">
                    Only registered tourist accounts may submit reviews.
                </div>
            @endif
        </div>
    </section>
</x-layout>
