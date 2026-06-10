<x-layout>
    <div class="section">
        <a href="{{ route('promo-packages.index') }}" class="btn btn-secondary" style="margin-bottom: 1rem;">← Back</a>
        <h1 class="title">{{ $promoPackage->name }}</h1>
        <p class="lead">Special discount offer for your Bolinao adventure</p>
    </div>

    <div style="max-width: 1200px; margin: 2rem auto;">
        <div style="background: linear-gradient(135deg, rgba(76, 175, 80, 0.3) 0%, rgba(17, 24, 68, 0.5) 100%); border: 1px solid rgba(114, 136, 174, 0.3); border-radius: 1.25rem; overflow: hidden; box-shadow: 0 8px 32px rgba(0, 0, 0, 0.3);">
            @if($promoPackage->image)
                <img src="{{ asset('storage/' . $promoPackage->image) }}" alt="{{ $promoPackage->name }}" style="width: 100%; height: 400px; object-fit: cover;">
            @else
                <div style="width: 100%; height: 400px; background: linear-gradient(135deg, rgba(76, 175, 80, 0.2) 0%, rgba(17, 24, 68, 0.4) 100%); display: flex; align-items: center; justify-content: center; color: #8890a8; font-size: 5rem;">🎉</div>
            @endif

            <div style="padding: 3rem;">
                <div style="display: flex; align-items: center; gap: 2rem; margin-bottom: 2rem;">
                    <div style="background: rgba(76, 175, 80, 0.2); color: #4CAF50; padding: 1rem 2rem; border-radius: 9999px; font-size: 2rem; font-weight: 800;">
                        {{ $promoPackage->discount_percentage }}% OFF
                    </div>
                    <div style="color: var(--palette-secondary); font-size: 1.125rem; font-weight: 600;">
                        Valid from {{ $promoPackage->start_date->format('M d, Y') }} to {{ $promoPackage->end_date->format('M d, Y') }}
                    </div>
                </div>

                <div style="margin-bottom: 2.5rem;">
                    <h3 style="font-size: 1.5rem; font-weight: 700; color: var(--palette-cream); margin-bottom: 1.5rem; line-height: 1.3;">About This Promo</h3>
                    <p style="color: var(--palette-secondary); font-size: 1.125rem; line-height: 1.9;">{{ $promoPackage->description }}</p>
                </div>

                <div style="display: flex; align-items: center; gap: 2rem; margin-bottom: 2.5rem;">
                    <div>
                        <h3 style="font-size: 1.25rem; font-weight: 700; color: var(--palette-cream); margin-bottom: 0.75rem;">Status</h3>
                        <span style="display: inline-block; padding: 0.75rem 1.5rem; border-radius: 9999px; font-size: 0.875rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em; {{ $promoPackage->isActive() ? 'background: rgba(129, 199, 132, 0.2); color: #81c784;' : 'background: rgba(136, 144, 168, 0.2); color: #8890a8;' }}">
                            {{ $promoPackage->isActive() ? 'Active' : 'Inactive' }}
                        </span>
                    </div>
                </div>

                <div style="display: flex; gap: 1rem;">
                    @auth
                        @if(auth()->user()->isGuest())
                            <button type="button" class="btn btn-primary" data-auth-open data-auth-mode="register" style="font-size: 1.125rem; padding: 1rem 2rem;">Register to Avail</button>
                        @else
                            <a href="{{ route('packages.index', ['promo' => $promoPackage->id]) }}" class="btn btn-primary" style="font-size: 1.125rem; padding: 1rem 2rem;">Avail This Promo</a>
                        @endif
                    @else
                        <a href="#" class="btn btn-primary" data-auth-open style="font-size: 1.125rem; padding: 1rem 2rem;">Login to Avail</a>
                    @endauth
                </div>
            </div>
        </div>
    </div>
</x-layout>
