<x-layout title="Tour Packages">

@php
    $touristUser = auth()->user()?->isTourist() ? auth()->user() : null;
    $selectedPromo = $selectedPromo ?? null;
    $promoActive = $selectedPromo?->isActive() ?? false;
    $selectedDuration = $selectedDuration ?? request('duration', 'all');
    if (request()->boolean('dur_1') && ! request()->boolean('dur_all')) {
        $selectedDuration = '1';
    } elseif (request()->boolean('dur_2') && ! request()->boolean('dur_all')) {
        $selectedDuration = '2_4';
    }
@endphp

<section class="packages-hero bolinao-hero">
    <div class="packages-hero-card bolinao-card">
        <div class="packages-hero-copy bolinao-copy">
            <span class="visual-tag">Explore Bolinao&apos;s Natural Beauty</span>
            <h1 class="visual-headline">Find your next tour in Bolinao</h1>
            <p class="visual-copy">
                Discover curated packages, unique experiences, and local adventures across Bolinao.
            </p>
            <div class="packages-hero-cta">Explore tours below</div>
        </div>
    </div>
</section>

<section class="packages-listing">
    <div class="packages-container">
        <aside class="packages-sidebar packages-search-panel">
            <h3>Search tour packages</h3>
            <form action="{{ route('packages.index') }}" method="GET" class="search-form packages-search-form">
                @if(request('promo'))
                    <input type="hidden" name="promo" value="{{ request('promo') }}">
                @endif
                <div class="form-group packages-search-field packages-search-field-wide">
                    <label for="search">Search tours</label>
                    <div class="search-input-wrap">
                        <span class="search-input-icon" aria-hidden="true"></span>
                        <input id="search" name="search" type="search" value="{{ request('search') }}" placeholder="Search by name, location, or experience" class="form-control" />
                    </div>
                </div>

                <div class="form-group packages-search-field">
                    <label for="category">Category</label>
                    <select id="category" name="category" class="form-control">
                        <option value="">All categories</option>
                        @foreach($categoryMap as $key => $cat)
                            <option value="{{ $key }}" {{ request('category') == $key ? 'selected' : '' }}>{{ $cat['label'] }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group packages-search-field packages-capacity-field">
                    <label for="capacity">Capacity</label>
                    <input id="capacity" name="capacity" type="number" min="1" step="1" value="{{ old('capacity', $capacity ?? '') }}" placeholder="Guests" aria-label="Minimum guest capacity" class="form-control" />
                </div>

                <div class="form-group duration-group packages-search-field">
                    <span class="duration-title">Duration</span>
                    <div class="duration-filter">
                        <label class="duration-option">
                            <input type="radio" name="duration" value="all" @checked(! in_array($selectedDuration, ['1', '2_4'], true))>
                            <span>All</span>
                        </label>
                        <label class="duration-option">
                            <input type="radio" name="duration" value="1" @checked($selectedDuration === '1')>
                            <span>1 Day</span>
                        </label>
                        <label class="duration-option">
                            <input type="radio" name="duration" value="2_4" @checked($selectedDuration === '2_4')>
                            <span>2-4 Days</span>
                        </label>
                    </div>
                </div>

                <button type="submit" class="search-btn packages-search-submit">Search</button>
            </form>
        </aside>

        <main class="packages-main">
            <div class="packages-header-row">
                <div>
                    <h2 class="section-title">Browse Tour Packages</h2>
                    <p class="section-copy">Choose from curated Bolinao packages designed for couples, families, and small groups.</p>
                </div>
            </div>

            @if($promoActive)
                <div class="card text-smoke mb-4" style="padding: 1rem; border-radius: 1rem; border: 1px solid rgba(255,255,255,0.08); background: rgba(10, 18, 40, 0.92);">
                    <strong>{{ $selectedPromo->name }}</strong> is active — enjoy {{ number_format($selectedPromo->discount_percentage, 0) }}% off.
                    @if($selectedPromo->minGuestCapacity())
                        <span>Showing packages for {{ $selectedPromo->minGuestCapacity() }} or more guests.</span>
                    @endif
                </div>
            @endif

            @if($packages->isEmpty())
                <div class="card text-center text-muted py-5">No packages found.</div>
            @else
                <div class="package-card-grid">
                    @foreach($packages as $package)
                        <article class="package-card destination-card">
                            <div class="package-card-media" style="background-image: url('{{ $package->image_url }}');">
                                <div class="badge-rating">{{ number_format($package->average_rating,1) }} ★</div>
                            </div>
                            <div class="package-card-body">
                                <div class="package-card-meta">
                                    <span>{{ $package->duration_days }} Day Tour</span>
                                    <span>·</span>
                                    <span>Up to {{ $package->max_guests }} guests</span>
                                    <span>·</span>
                                    <span>{{ $package->time_start_formatted }} - {{ $package->time_end_formatted }}</span>
                                    <span>&middot;</span>
                                    <span>{{ $package->location }}</span>
                                </div>
                                <h3 class="package-card-title">{{ $package->name }}</h3>
                                <p class="package-card-description">{{ Str::limit($package->description, 110) }}</p>
                                <div style="flex:1"></div>
                                <div class="package-card-footer">
                                    <div class="package-card-price">
                                    @if($promoActive)
                                        @php
                                            $discountedPrice = $selectedPromo->discountedPrice($package->price);
                                        @endphp
                                        <span class="price discounted">₱{{ number_format($discountedPrice, 2) }}</span>
                                        <span class="price-original" style="font-size:0.85rem; text-decoration: line-through; color: rgba(255,255,255,0.65);">₱{{ number_format($package->price, 2) }}</span>
                                    @else
                                        <span class="price">₱{{ number_format($package->price) }}</span>
                                    @endif
                                    <span class="price-note">/ person</span>
                                </div>
                                    <div class="package-card-actions">
                                        <a href="{{ route('packages.show', array_merge([$package], request()->only('promo'))) }}" class="btn btn-secondary">View details</a>
                                        @if($touristUser)
                                            <a href="{{ route('packages.show', array_merge([$package], request()->only('promo'))) }}" class="btn">Book</a>
                                        @else
                                            <a href="#" class="btn" data-auth-open>Book</a>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </article>
                    @endforeach
                </div>

            @endif
        </main>
    </div>
    </section>

</x-layout>
