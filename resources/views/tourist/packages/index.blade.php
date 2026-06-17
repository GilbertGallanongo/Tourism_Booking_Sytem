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

<style>
body.packages-shell {
    background: #030712;
}

body.packages-shell::before {
    content: "";
    position: fixed;
    inset: 0;
    z-index: -2;
    background:
        linear-gradient(rgba(2, 6, 23, 0.42), rgba(2, 6, 23, 0.54)),
        url("/images/bolinao-church.jpg") center top / cover no-repeat;
    pointer-events: none;
}

body.packages-shell .topbar {
    position: sticky;
    top: 0;
    z-index: 100;
    min-height: 1.8rem;
    background: rgba(7, 14, 38, 0.96) !important;
    border-bottom: 1px solid rgba(255, 255, 255, 0.08);
    box-shadow: none;
}

body.packages-shell .topbar .frame {
    width: min(100%, 72rem);
    padding: 0 1rem;
}

body.packages-shell .bolinao-nav {
    min-height: 1.8rem;
    align-items: center;
}

body.packages-shell .bolinao-brand,
body.packages-shell .bolinao-navlinks a,
body.packages-shell .bolinao-navlinks button {
    font-size: 0.72rem;
    font-weight: 900;
}

body.packages-shell .bolinao-navlinks a,
body.packages-shell .bolinao-navlinks button {
    min-height: 1.55rem;
    padding: 0.22rem 0.72rem;
    border-radius: 0.35rem;
}

body.packages-shell .bolinao-button-light {
    min-width: auto;
    min-height: 1.45rem;
    padding: 0.22rem 0.85rem;
    border-radius: 999px;
    font-size: 0.7rem;
}

body.packages-shell .content {
    width: 100%;
    max-width: none;
    padding: 0 1rem 3rem;
}

.tour-packages-page {
    width: min(100%, 72rem);
    margin: 0 auto;
    color: #f8fafc;
}

.tour-packages-page .packages-hero {
    min-height: auto;
    display: flex;
    justify-content: center;
    padding: 6.5rem 0 6rem;
}

.tour-packages-page .packages-hero-card {
    width: min(100%, 57rem);
    min-height: 17rem;
    margin: 0 auto;
    padding: clamp(2rem, 4vw, 3rem);
    border: 1px solid rgba(148, 163, 184, 0.26);
    border-radius: 1.25rem;
    background: rgba(12, 24, 58, 0.88);
    box-shadow: 0 1.5rem 3.5rem rgba(0, 0, 0, 0.34);
    backdrop-filter: blur(8px);
}

.tour-packages-page .packages-hero-card::before,
.tour-packages-page .packages-hero-card::after {
    display: none;
}

.tour-packages-page .packages-hero-copy {
    position: relative;
    z-index: 1;
    width: min(100%, 31rem);
    max-width: none;
}

.tour-packages-page .visual-tag,
.tour-packages-page .visual-headline,
.tour-packages-page .visual-copy {
    position: static !important;
    left: auto;
    right: auto;
    bottom: auto;
}

.tour-packages-page .visual-tag {
    display: inline-flex;
    width: fit-content;
    margin-bottom: 1rem;
    padding: 0.42rem 0.85rem;
    border-radius: 999px;
    color: #f8fafc;
    background: rgba(148, 163, 184, 0.22);
    font-size: 0.68rem;
    font-weight: 900;
    letter-spacing: 0;
    text-transform: uppercase;
}

.tour-packages-page .visual-headline {
    max-width: 26rem;
    margin: 0;
    color: #f3eadb;
    font-size: clamp(2.35rem, 5vw, 3.55rem);
    font-weight: 1000;
    line-height: 1.03;
}

.tour-packages-page .visual-copy {
    max-width: 24rem;
    margin: 1rem 0 0;
    padding-left: 0.8rem;
    border-left: 1px solid rgba(248, 250, 252, 0.65);
    color: #eef2ff;
    font-size: 0.78rem;
    line-height: 1.55;
}

.tour-packages-page .packages-hero-cta {
    margin-top: 0.6rem;
    color: #ffffff;
    font-size: 0.72rem;
    font-weight: 800;
}

.tour-packages-page .packages-listing {
    margin-top: 0;
}

.tour-packages-page .packages-container {
    width: min(100%, 58.5rem);
    margin: 0 auto;
}

.tour-packages-page .packages-search-panel {
    width: 100%;
    margin: 0 0 1.9rem;
    padding: 1.75rem;
    border: 1px solid rgba(148, 163, 184, 0.28);
    border-radius: 0.9rem;
    background: rgba(3, 10, 28, 0.9);
    box-shadow: 0 1.2rem 2.8rem rgba(0, 0, 0, 0.34);
    backdrop-filter: blur(8px);
}

.tour-packages-page .packages-search-panel h3 {
    margin: 0 0 1.2rem;
    color: #ffffff;
    font-size: 1.35rem;
    font-weight: 900;
}

.tour-packages-page .packages-search-form {
    display: grid;
    grid-template-columns: minmax(0, 1fr) 12rem 7rem;
    gap: 0.9rem;
    align-items: end;
}

.tour-packages-page .packages-search-field-wide,
.tour-packages-page .duration-group {
    grid-column: span 1;
}

.tour-packages-page .duration-group {
    grid-column: 1 / span 2;
}

.tour-packages-page .packages-search-submit {
    justify-self: end;
    align-self: end;
}

.tour-packages-page .packages-search-field label,
.tour-packages-page .duration-title {
    display: block;
    margin-bottom: 0.5rem;
    color: #ffffff;
    font-size: 0.92rem;
    font-weight: 900;
}

.tour-packages-page .search-input-wrap {
    position: relative;
}

.tour-packages-page .search-input-icon {
    position: absolute;
    left: 0.75rem;
    top: 50%;
    width: 0.86rem;
    height: 0.86rem;
    transform: translateY(-50%);
    border: 2px solid rgba(226, 232, 240, 0.75);
    border-radius: 999px;
    pointer-events: none;
}

.tour-packages-page .search-input-icon::after {
    content: "";
    position: absolute;
    right: -0.36rem;
    bottom: -0.26rem;
    width: 0.45rem;
    height: 2px;
    border-radius: 999px;
    background: rgba(226, 232, 240, 0.75);
    transform: rotate(45deg);
}

.tour-packages-page .search-input-wrap input {
    padding-left: 2.1rem;
}

.tour-packages-page .form-control {
    width: 100%;
    min-height: 2.35rem;
    padding: 0.55rem 0.75rem;
    border: 1px solid rgba(128, 151, 202, 0.74);
    border-radius: 0.42rem;
    color: #f8fafc;
    background: rgba(8, 15, 38, 0.72);
    font-size: 0.78rem;
    outline: none;
}

.tour-packages-page .form-control::placeholder {
    color: rgba(226, 232, 240, 0.72);
}

.tour-packages-page .duration-filter {
    display: flex;
    flex-wrap: wrap;
    gap: 0.65rem;
}

.tour-packages-page .duration-option {
    display: inline-flex;
    align-items: center;
    gap: 0.35rem;
    color: #ffffff;
    font-size: 0.9rem;
    font-weight: 900;
}

.tour-packages-page .duration-option input {
    width: 0.95rem;
    height: 0.95rem;
    accent-color: #a7bf94;
}

.tour-packages-page .search-btn {
    min-width: 4.75rem;
    min-height: 2.35rem;
    padding: 0.55rem 1rem;
    border: 0;
    border-radius: 0.65rem;
    color: #0f172a;
    background: #f1f5f9;
    font-size: 0.7rem;
    font-weight: 900;
    box-shadow: 0 0.7rem 1.4rem rgba(15, 23, 42, 0.28);
}

.tour-packages-page .packages-main {
    width: 100%;
}

.tour-packages-page .packages-header-row {
    margin-bottom: 0.75rem;
}

.tour-packages-page .section-title {
    margin: 0;
    color: #f3eadb;
    font-size: 1rem;
    font-weight: 900;
}

.tour-packages-page .section-copy {
    margin: 0.55rem 0 0;
    color: #ffffff;
    font-size: 0.72rem;
    font-weight: 700;
}

.tour-packages-page .package-card-grid {
    display: grid;
    grid-template-columns: repeat(3, minmax(0, 1fr));
    gap: 1.15rem;
}

.tour-packages-page .package-card {
    min-width: 0;
    overflow: hidden;
    border: 1px solid rgba(148, 163, 184, 0.26);
    border-radius: 0.8rem;
    background: rgba(8, 18, 44, 0.9);
    box-shadow: 0 1rem 2.2rem rgba(0, 0, 0, 0.3);
}

.tour-packages-page .package-card-media {
    position: relative;
    min-height: 10.25rem;
    background-size: cover;
    background-position: center;
}

.tour-packages-page .badge-rating {
    position: absolute;
    top: 0.65rem;
    left: 0.65rem;
    padding: 0.24rem 0.46rem;
    border-radius: 999px;
    color: #ffffff;
    background: rgba(15, 23, 42, 0.72);
    font-size: 0.68rem;
    font-weight: 900;
}

.tour-packages-page .package-card-body {
    padding: 0.9rem;
}

.tour-packages-page .package-card-title {
    color: #ffffff;
    font-size: 1rem;
}

.tour-packages-page .package-card-meta,
.tour-packages-page .package-card-description,
.tour-packages-page .package-card-price,
.tour-packages-page .package-card-actions .btn {
    font-size: 0.72rem;
}

@media (max-width: 900px) {
    .tour-packages-page .packages-hero {
        padding-top: 4.5rem;
    }

    .tour-packages-page .packages-search-form,
    .tour-packages-page .package-card-grid {
        grid-template-columns: 1fr;
    }

    .tour-packages-page .duration-group,
    .tour-packages-page .packages-search-field-wide {
        grid-column: auto;
    }

    .tour-packages-page .packages-search-submit {
        justify-self: stretch;
    }

    .tour-packages-page .search-btn {
        width: 100%;
    }
}

@media (max-width: 640px) {
    body.packages-shell .content {
        padding-inline: 0.7rem;
    }

    .tour-packages-page .packages-hero {
        padding: 2.5rem 0 2rem;
    }

    .tour-packages-page .packages-hero-card,
    .tour-packages-page .packages-search-panel {
        padding: 1rem;
    }

    .tour-packages-page .visual-headline {
        font-size: 2rem;
    }
}
</style>

<div class="tour-packages-page">
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
</div>

</x-layout>
