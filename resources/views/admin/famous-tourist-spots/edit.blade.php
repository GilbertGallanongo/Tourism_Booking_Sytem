<x-layout>
    <div class="section">
        <h1 class="title">Edit Famous Tourist Spot</h1>
        <p class="lead">Update the details of this famous tourist spot.</p>
    </div>

    <div class="card">
        <form method="POST" action="{{ route('admin.famous-tourist-spots.update', $famousTouristSpot) }}" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div style="margin-bottom: 1.5rem;">
                <label style="display: block; margin-bottom: 0.5rem; font-weight: 600; color: white;">Name *</label>
                <input type="text" name="name" value="{{ old('name', $famousTouristSpot->name) }}" required style="width: 100%; padding: 0.75rem; border: 1px solid #3d3d5c; border-radius: 0.5rem; background: #1a1a2e; color: white;" placeholder="Enter tourist spot name">
                @error('name')
                    <div style="color: #dc3545; font-size: 0.875rem; margin-top: 0.25rem;">{{ $message }}</div>
                @enderror
            </div>

            <div style="margin-bottom: 1.5rem;">
                <label style="display: block; margin-bottom: 0.5rem; font-weight: 600; color: white;">Description *</label>
                <textarea name="description" required rows="4" style="width: 100%; padding: 0.75rem; border: 1px solid #3d3d5c; border-radius: 0.5rem; background: #1a1a2e; color: white;" placeholder="Enter tourist spot description">{{ old('description', $famousTouristSpot->description) }}</textarea>
                @error('description')
                    <div style="color: #dc3545; font-size: 0.875rem; margin-top: 0.25rem;">{{ $message }}</div>
                @enderror
            </div>

            <div style="margin-bottom: 1.5rem;">
                <label style="display: block; margin-bottom: 0.5rem; font-weight: 600; color: white;">Location *</label>
                <input type="text" name="location" value="{{ old('location', $famousTouristSpot->location) }}" required style="width: 100%; padding: 0.75rem; border: 1px solid #3d3d5c; border-radius: 0.5rem; background: #1a1a2e; color: white;" placeholder="Enter location">
                @error('location')
                    <div style="color: #dc3545; font-size: 0.875rem; margin-top: 0.25rem;">{{ $message }}</div>
                @enderror
            </div>

            <div style="margin-bottom: 1.5rem;">
                <label style="display: block; margin-bottom: 0.5rem; font-weight: 600; color: white;">Current Image</label>
                @if($famousTouristSpot->image)
                    <img src="{{ asset('storage/' . $famousTouristSpot->image) }}" alt="{{ $famousTouristSpot->name }}" style="width: 150px; height: 150px; object-fit: cover; border-radius: 0.5rem; margin-bottom: 0.5rem;">
                @else
                    <div style="width: 150px; height: 150px; background: #3d3d5c; border-radius: 0.5rem; display: flex; align-items: center; justify-content: center; color: #8890a8; margin-bottom: 0.5rem;">No Image</div>
                @endif
            </div>

            <div style="margin-bottom: 1.5rem;">
                <label style="display: block; margin-bottom: 0.5rem; font-weight: 600; color: white;">New Image</label>
                <input type="file" name="image" accept="image/jpeg,image/png,image/jpg,image/gif,image/webp" style="width: 100%; padding: 0.75rem; border: 1px solid #3d3d5c; border-radius: 0.5rem; background: #1a1a2e; color: white;">
                <div style="font-size: 0.8rem; color: #8890a8; margin-top: 0.25rem;">Leave empty to keep current image. Maximum file size: 5MB. Allowed formats: JPEG, PNG, JPG, GIF, WebP</div>
                @error('image')
                    <div style="color: #dc3545; font-size: 0.875rem; margin-top: 0.25rem;">{{ $message }}</div>
                @enderror
            </div>

            <div style="margin-bottom: 1.5rem;">
                <label style="display: block; margin-bottom: 0.5rem; font-weight: 600; color: white;">Status</label>
                <div style="display: flex; align-items: center; gap: 0.5rem;">
                    <input type="checkbox" name="is_active" value="1" {{ old('is_active', $famousTouristSpot->is_active) ? 'checked' : '' }} style="width: 1.25rem; height: 1.25rem; cursor: pointer;">
                    <span style="color: white;">Active (visible to tourists)</span>
                </div>
            </div>

            <div style="margin-bottom: 1.5rem;">
                <label style="display: block; margin-bottom: 0.5rem; font-weight: 600; color: white;">Sort Order</label>
                <input type="number" name="sort_order" value="{{ old('sort_order', $famousTouristSpot->sort_order) }}" style="width: 100%; padding: 0.75rem; border: 1px solid #3d3d5c; border-radius: 0.5rem; background: #1a1a2e; color: white;" placeholder="0 = highest priority">
                <div style="font-size: 0.8rem; color: #8890a8; margin-top: 0.25rem;">Lower numbers appear first</div>
                @error('sort_order')
                    <div style="color: #dc3545; font-size: 0.875rem; margin-top: 0.25rem;">{{ $message }}</div>
                @enderror
            </div>

            <div style="display: flex; gap: 1rem; margin-top: 2rem;">
                <button type="submit" class="btn btn-primary">Update Tourist Spot</button>
                <a href="{{ route('admin.famous-tourist-spots.index') }}" class="btn btn-outline-secondary">Cancel</a>
            </div>
        </form>
    </div>
</x-layout>
