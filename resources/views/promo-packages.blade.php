<x-layout>
    <div class="section">
        <h1 class="title">Promo Packages</h1>
        <p class="lead">Special offers and discounts for your Bolinao adventure.</p>
    </div>

    <div class="package-card-grid" style="margin-top: 2rem;">
        @forelse($promoPackages as $promo)
            <article class="package-card">
                @if($promo->image)
                    <div class="package-card-media" style="background-image: url('{{ asset('storage/' . $promo->image) }}');"></div>
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
            <div style="grid-column: 1 / -1; text-align: center; padding: 4rem 2rem; color: #8890a8;">
                <div style="font-size: 3rem; margin-bottom: 1rem;">🎉</div>
                <p style="font-size: 1.125rem;">No promo packages available at the moment.</p>
                <p style="font-size: 0.875rem; margin-top: 0.5rem;">Check back later for exciting deals!</p>
            </div>
        @endforelse
    </div>
</x-layout>
