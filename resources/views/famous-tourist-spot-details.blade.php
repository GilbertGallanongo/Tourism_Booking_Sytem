<x-layout>
    <div class="section">
        <a href="{{ url()->previous() }}" class="btn btn-secondary" style="margin-bottom: 1rem;">← Back</a>
        <h1 class="title">{{ $spot->name }}</h1>
        <p class="lead">Discover this amazing tourist destination in Bolinao</p>
    </div>

    <div style="max-width: 1200px; margin: 2rem auto;">
        <div style="background: linear-gradient(135deg, rgba(75, 86, 148, 0.3) 0%, rgba(17, 24, 68, 0.5) 100%); border: 1px solid rgba(114, 136, 174, 0.3); border-radius: 1.25rem; overflow: hidden; box-shadow: 0 8px 32px rgba(0, 0, 0, 0.3);">
            @if($spot->image)
                <img src="{{ asset('storage/' . $spot->image) }}" alt="{{ $spot->name }}" style="width: 100%; height: 500px; object-fit: cover;">
            @else
                <div style="width: 100%; height: 500px; background: linear-gradient(135deg, rgba(75, 86, 148, 0.2) 0%, rgba(17, 24, 68, 0.4) 100%); display: flex; align-items: center; justify-content: center; color: #8890a8; font-size: 5rem;">🗺️</div>
            @endif

            <div style="padding: 3rem;">
                <div style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 2rem; color: var(--palette-secondary); font-size: 1.125rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em;">
                    <svg style="width: 1.5rem; height: 1.5rem;" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd" />
                    </svg>
                    {{ $spot->location }}
                </div>

                <div style="margin-bottom: 2.5rem;">
                    <h3 style="font-size: 1.5rem; font-weight: 700; color: var(--palette-cream); margin-bottom: 1.5rem; line-height: 1.3;">About This Spot</h3>
                    <p style="color: var(--palette-secondary); font-size: 1.125rem; line-height: 1.9;">{{ $spot->description }}</p>
                </div>

                <div style="display: flex; align-items: center; gap: 2rem; margin-bottom: 2.5rem;">
                    <div>
                        <h3 style="font-size: 1.25rem; font-weight: 700; color: var(--palette-cream); margin-bottom: 0.75rem;">Status</h3>
                        <span style="display: inline-block; padding: 0.75rem 1.5rem; border-radius: 9999px; font-size: 0.875rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em; {{ $spot->is_active ? 'background: rgba(129, 199, 132, 0.2); color: #81c784;' : 'background: rgba(136, 144, 168, 0.2); color: #8890a8;' }}">
                            {{ $spot->is_active ? 'Active' : 'Inactive' }}
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-layout>
