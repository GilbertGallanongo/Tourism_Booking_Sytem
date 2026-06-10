<x-layout :title="$tourPackage->name">
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
                                @if(auth()->id() === $review->user_id)
                                    <form action="{{ route('reviews.destroy', $review) }}" method="POST" class="package-detail-delete-form">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit">Delete review</button>
                                    </form>
                                @endif
                            </article>
                        @endforeach
                    </div>
                @endif
            </section>

            @if(auth()->check() && auth()->user()->isTourist())
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
                    Only tourist accounts may submit reviews. Admins cannot rate tour packages.
                </div>
            @endif
        </div>
    </section>
</x-layout>
