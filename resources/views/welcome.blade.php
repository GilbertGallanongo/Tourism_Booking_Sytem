<x-layout>
    <style>
        /* Force equal-height package cards and consistent media sizing */
        .homepage-slider .package-card { display: flex; flex-direction: column; height: 100%; }
        .homepage-slider .package-card-media { flex: 0 0 9.5rem; width: 100%; background-size: cover; background-position: center; }
        .homepage-slider .package-card-body { flex: 1 1 auto; display: flex; flex-direction: column; justify-content: space-between; }
    </style>

    <section class="homepage-slider" aria-label="Homepage slider">
        <div class="slider-view">
            <div class="slider-track">
                <section class="slider-slide slide-hero">
                    <section class="bolinao-hero" aria-label="Bolinao tourism landing page">
                        <div class="bolinao-card">
                            <div class="bolinao-hero-grid">
                                <div class="bolinao-copy">
                                    <p class="bolinao-kicker">Heritage</p>
                                    <h1>Bolinao</h1>
                                    <p>
                                        Visit centuries-old stone landmarks, coastal villages, beaches,
                                        caves, and quiet Pangasinan views in one memorable trip.
                                    </p>

                                    <div class="bolinao-actions">
                                        @auth
                                            @if(auth()->user()->isGuest())
                                                <button type="button" class="bolinao-button bolinao-button-light" data-auth-open data-auth-mode="register">Book a Trip Now</button>
                                            @else
                                                <a href="{{ route('packages.index') }}" class="bolinao-button bolinao-button-light">Book a Trip Now</a>
                                            @endif
                                        @else
                                            <a href="#" class="bolinao-button bolinao-button-light" data-auth-open>Book a Trip Now</a>
                                        @endauth
                                        <a href="{{ route('packages.index') }}" class="bolinao-button bolinao-button-outline">Browse Tour Packages</a>
                                    </div>
                                </div>

                                <div class="bolinao-hero-visual" aria-hidden="true"></div>
                            </div>
                        </div>
                    </section>
                </section>

                <section class="slider-slide slide-packages featured-packages" aria-label="Featured high-rated packages">
                    <div class="featured-packages-inner">
                        <div class="section-header">
                            <p>Traveler Favorites</p>
                            <h2>Top-Rated Tour Packages</h2>
                            <p>Discover the most popular experiences loved by our guests</p>
                        </div>

                        <div class="package-card-grid">
                            @forelse($topRatedPackages as $package)
                                <article class="package-card">
                                    <div class="package-card-media has-card-image" style="--card-image: url('{{ $package->image_url }}'); background-image: var(--card-image);"></div>
                                    <div class="package-card-body">
                                        <div class="package-card-meta">
                                            <span>{{ $package->duration_days }} Day Tour</span>
                                            <span>{{ $package->location }}</span>
                                            <span>Max {{ $package->max_guests }} guests</span>
                                        </div>
                                        <h3 class="package-card-title">{{ $package->name }}</h3>
                                        <p class="package-card-description">{{ Str::limit($package->description, 110) }}</p>
                                        <div class="package-card-rating">
                                            @for($i = 1; $i <= 5; $i++)
                                                {!! $i <= round($package->rating) ? '&#9733;' : '&#9734;' !!}
                                            @endfor
                                            <span class="rating-text">({{ number_format($package->rating, 1) }})</span>
                                        </div>
                                        <div class="package-card-footer">
                                            <div class="package-card-price">
                                                <span class="price">&#8369;{{ number_format($package->price) }}</span>
                                                <span class="price-note">/ person</span>
                                            </div>
                                            <div class="package-card-actions">
                                                <a href="{{ route('packages.show', $package) }}" class="btn btn-secondary">View details</a>
                                                <a href="{{ route('packages.index') }}" class="btn btn-primary">Browse more</a>
                                            </div>
                                        </div>
                                    </div>
                                </article>
                            @empty
                                <p style="grid-column: 1 / -1; text-align: center; padding: 2rem;">No top-rated packages available yet. Check back soon!</p>
                            @endforelse
                        </div>

                        <div style="text-align: center; margin-top: 2rem;">
                            <a href="{{ route('packages.index') }}" class="btn btn-primary">View All Packages</a>
                        </div>
                    </div>
                </section>

                <section class="slider-slide slide-reviews customer-reviews-section" aria-label="Customer reviews and testimonials">
                    <div class="reviews-container">
                        <div class="reviews-header">
                            <p>What Our Travelers Say</p>
                            <h2>Customer Reviews & Testimonials</h2>
                            <p class="reviews-header-desc">Hear from real travelers who've experienced the beauty and adventure of Bolinao with us</p>
                        </div>

                        <div class="reviews-grid">
                            @forelse($customerReviews as $review)
                                <div class="review-card">
                                    <div class="review-stars">
                                        @for($i = 1; $i <= 5; $i++)
                                            {!! $i <= round($review->rating) ? '&#9733;' : '&#9734;' !!}
                                        @endfor
                                    </div>
                                    <p class="review-text">"{{ $review->comment }}"</p>
                                    <div class="review-author">
                                        <div class="review-avatar">
                                            {{ strtoupper(substr($review->user->name ?? 'Guest', 0, 1)) }}
                                        </div>
                                        <div class="review-info">
                                            <h4>{{ $review->user->name ?? 'Guest Traveler' }}</h4>
                                            <p>{{ $review->tourPackage->name ?? 'Tour Guest' }}</p>
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <div style="grid-column: 1 / -1; text-align: center; padding: 2rem; color: rgba(234, 224, 207, 0.6);">
                                    <p>Reviews coming soon from our travelers!</p>
                                </div>
                            @endforelse
                        </div>
                    </div>
                </section>
            </div>
        </div>
    </section>

    <section class="famous-tourist-spots" aria-label="Famous tourist spots in Bolinao" style="padding: 5rem 1.5rem 3rem;">
        <div class="featured-packages-inner">
            <div class="section-header">
                <p>Explore Bolinao</p>
                <h2>Famous Tourist Spots</h2>
                <p>Discover the most popular destinations and attractions in Bolinao</p>
            </div>

            <div class="package-card-grid">
                @forelse($famousTouristSpots->take(3) as $spot)
                    <article class="package-card">
                        <div class="package-card-media has-card-image" style="--card-image: url('{{ $spot->image_url }}'); background-image: var(--card-image);"></div>
                        <div class="package-card-body">
                            <div class="package-card-meta">
                                <span>📍 {{ $spot->location }}</span>
                                <span>{{ $spot->is_active ? 'Active' : 'Inactive' }}</span>
                            </div>
                            <h3 class="package-card-title">{{ $spot->name }}</h3>
                            <p class="package-card-description">{{ Str::limit($spot->description, 110) }}</p>
                            <div class="package-card-footer">
                                <a href="{{ route('famous-tourist-spots.show', $spot->id) }}" class="btn btn-secondary">View details</a>
                            </div>
                        </div>
                    </article>
                @empty
                    <p style="grid-column: 1 / -1; text-align: center; padding: 2rem;">No famous tourist spots available yet. Check back soon!</p>
                @endforelse
            </div>

            <div style="text-align: center; margin-top: 2rem;">
                <a href="{{ route('famous-tourist-spots.index') }}" class="btn btn-primary">View All Famous Tourist Spots</a>
            </div>
        </div>
    </section>

    <section class="promo-packages-section" aria-label="Promo packages and special offers" style="padding: 5rem 1.5rem 3rem;">
        <div class="featured-packages-inner">
            <div class="section-header">
                <p>Special Offers</p>
                <h2>Promo Packages</h2>
                <p>Exclusive discounts and special deals for your Bolinao adventure</p>
            </div>

            <div class="package-card-grid">
                @forelse($promoPackages->take(3) as $promo)
                    <article class="package-card">
                        @if($promo->image)
                            <div class="package-card-media has-card-image" style="--card-image: url('{{ $promo->image_url }}'); background-image: var(--card-image);"></div>
                        @else
                            <div class="package-card-media" style="background: linear-gradient(135deg, rgba(76, 175, 80, 0.3) 0%, rgba(17, 24, 68, 0.5) 100%); display: flex; align-items: center; justify-content: center;">
                                <div style="font-size: 4rem; opacity: 0.5;">🎉</div>
                            </div>
                        @endif
                        <div class="package-card-body">
                            <div class="package-card-meta">
                                <span>🏷️ {{ $promo->discount_percentage }}% OFF</span>
                                <span>Valid until {{ $promo->end_date->format('M d, Y') }}</span>
                            </div>
                            <h3 class="package-card-title">{{ $promo->name }}</h3>
                            <p class="package-card-description">{{ Str::limit($promo->description, 110) }}</p>
                            <div class="package-card-footer" style="text-align: center;">
                                <a href="{{ route('promo-packages.show', $promo->id) }}" class="btn btn-secondary">View Details</a>
                                @auth
                                    @if(auth()->user()->isGuest())
                                        <button type="button" class="btn btn-primary" data-auth-open data-auth-mode="register">Avail Promo</button>
                                    @else
                                        <a href="{{ route('packages.index', ['promo' => $promo->id]) }}" class="btn btn-primary">Avail Promo</a>
                                    @endif
                                @else
                                    <a href="#" class="btn btn-primary" data-auth-open>Avail Promo</a>
                                @endauth
                            </div>
                        </div>
                    </article>
                @empty
                    <p style="grid-column: 1 / -1; text-align: center; padding: 2rem;">No promo packages available at the moment. Check back soon!</p>
                @endforelse
            </div>

            <div style="text-align: center; margin-top: 2rem;">
                <a href="{{ route('promo-packages.index') }}" class="btn btn-primary">View All Promo Packages</a>
            </div>
        </div>
    </section>

</x-layout>
