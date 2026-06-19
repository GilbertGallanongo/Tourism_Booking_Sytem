<x-layout>
    <div class="section">
        <h1 class="title">Famous Tourist Spots</h1>
        <p class="lead">Discover the most popular tourist destinations in the region.</p>
    </div>

    <div class="package-card-grid" style="margin-top: 2rem;">
        @forelse($spots as $spot)
            <article class="package-card">
                <x-image-view-link :src="$spot->image_url" :title="$spot->name" class="package-card-media" style="background-image: url('{{ $spot->image_url }}');"></x-image-view-link>
                <div class="package-card-body">
                    <div class="package-card-meta">
                        <span>📍 {{ $spot->location }}</span>
                        <span>{{ $spot->is_active ? 'Active' : 'Inactive' }}</span>
                    </div>
                    <h3 class="package-card-title">{{ $spot->name }}</h3>
                    <p class="package-card-description">{{ Str::limit($spot->description, 110) }}</p>
                    <div class="package-card-footer">
                        <a href="{{ route('famous-tourist-spots.show', $spot->id) }}" class="btn btn-primary">View details</a>
                    </div>
                </div>
            </article>
        @empty
            <div style="grid-column: 1 / -1; text-align: center; padding: 4rem 2rem; color: #8890a8;">
                <div style="font-size: 3rem; margin-bottom: 1rem;">🗺️</div>
                <p style="font-size: 1.125rem;">No famous tourist spots available at the moment.</p>
                <p style="font-size: 0.875rem; margin-top: 0.5rem;">Check back later for exciting destinations!</p>
            </div>
        @endforelse
    </div>
</x-layout>
