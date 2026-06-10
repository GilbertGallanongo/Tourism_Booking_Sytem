<x-layout>
    <div class="section">
        <h1 class="title">Add Famous Tourist Spot</h1>
        <p class="lead">Create a new famous tourist spot to showcase to tourists and guests.</p>
    </div>

    <div class="card">
        <form method="POST" action="{{ route('admin.famous-tourist-spots.store') }}" enctype="multipart/form-data">
            @csrf

            <div style="margin-bottom: 1.5rem;">
                <label style="display: block; margin-bottom: 0.5rem; font-weight: 600; color: white;">Name *</label>
                <input type="text" name="name" required style="width: 100%; padding: 0.75rem; border: 1px solid #3d3d5c; border-radius: 0.5rem; background: #1a1a2e; color: white;" placeholder="Enter tourist spot name">
                @error('name')
                    <div style="color: #dc3545; font-size: 0.875rem; margin-top: 0.25rem;">{{ $message }}</div>
                @enderror
            </div>

            <div style="margin-bottom: 1.5rem;">
                <label style="display: block; margin-bottom: 0.5rem; font-weight: 600; color: white;">Description *</label>
                <textarea name="description" required rows="4" style="width: 100%; padding: 0.75rem; border: 1px solid #3d3d5c; border-radius: 0.5rem; background: #1a1a2e; color: white;" placeholder="Enter tourist spot description"></textarea>
                @error('description')
                    <div style="color: #dc3545; font-size: 0.875rem; margin-top: 0.25rem;">{{ $message }}</div>
                @enderror
            </div>

            <div style="margin-bottom: 1.5rem;">
                <label style="display: block; margin-bottom: 0.5rem; font-weight: 600; color: white;">Location *</label>
                <input type="text" name="location" required style="width: 100%; padding: 0.75rem; border: 1px solid #3d3d5c; border-radius: 0.5rem; background: #1a1a2e; color: white;" placeholder="Enter location">
                @error('location')
                    <div style="color: #dc3545; font-size: 0.875rem; margin-top: 0.25rem;">{{ $message }}</div>
                @enderror
            </div>

            <div style="margin-bottom: 1.5rem;">
                <label style="display: block; margin-bottom: 0.5rem; font-weight: 600; color: white;">Image</label>
                <input type="file" name="image" accept="image/jpeg,image/png,image/jpg,image/gif,image/webp" style="width: 100%; padding: 0.75rem; border: 1px solid #3d3d5c; border-radius: 0.5rem; background: #1a1a2e; color: white;">
                <div style="font-size: 0.8rem; color: #8890a8; margin-top: 0.25rem;">Maximum file size: 5MB. Allowed formats: JPEG, PNG, JPG, GIF, WebP</div>
                @error('image')
                    <div style="color: #dc3545; font-size: 0.875rem; margin-top: 0.25rem;">{{ $message }}</div>
                @enderror
            </div>

            <div style="margin-bottom: 1.5rem;">
                <label style="display: block; margin-bottom: 0.5rem; font-weight: 600; color: white;">Status</label>
                <div style="display: flex; align-items: center; gap: 0.5rem;">
                    <input type="checkbox" name="is_active" value="1" checked style="width: 1.25rem; height: 1.25rem; cursor: pointer;">
                    <span style="color: white;">Active (visible to tourists)</span>
                </div>
            </div>

            <div style="margin-bottom: 1.5rem;">
                <label style="display: block; margin-bottom: 0.5rem; font-weight: 600; color: white;">Sort Order</label>
                <input type="number" name="sort_order" value="0" style="width: 100%; padding: 0.75rem; border: 1px solid #3d3d5c; border-radius: 0.5rem; background: #1a1a2e; color: white;" placeholder="0 = highest priority">
                <div style="font-size: 0.8rem; color: #8890a8; margin-top: 0.25rem;">Lower numbers appear first</div>
                @error('sort_order')
                    <div style="color: #dc3545; font-size: 0.875rem; margin-top: 0.25rem;">{{ $message }}</div>
                @enderror
            </div>

            <div style="display: flex; gap: 1rem; margin-top: 2rem;">
                <button type="submit" class="btn btn-primary">Create Tourist Spot</button>
                <a href="{{ route('admin.famous-tourist-spots.index') }}" class="btn btn-outline-secondary">Cancel</a>
            </div>
        </form>
    </div>
</x-layout>
