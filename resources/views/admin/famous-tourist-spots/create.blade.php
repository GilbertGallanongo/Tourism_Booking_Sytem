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
                <input type="file" name="image" id="image_file" accept="image/jpeg,image/png,image/jpg,image/gif,image/webp" style="width: 100%; padding: 0.75rem; border: 1px solid #3d3d5c; border-radius: 0.5rem; background: #1a1a2e; color: white;">
                <div style="font-size: 0.8rem; color: #8890a8; margin-top: 0.25rem;">Maximum file size: 5MB. Allowed formats: JPEG, PNG, JPG, GIF, WebP</div>
                <div id="file-info" style="font-size: 0.85rem; color: #4CAF50; margin-top: 0.5rem; display: none;"></div>
                <div style="margin-top: 0.75rem;">
                    <img id="image_preview" src="data:image/svg+xml,%3Csvg xmlns=%22http://www.w3.org/2000/svg%22 width=%22150%22 height=%22120%22 fill=%22%232a2a4a%22%3E%3Crect width=%22150%22 height=%22120%22/%3E%3Ctext x=%2250%25%22 y=%2250%25%22 font-size=%2212%22 fill=%22%23888%22 text-anchor=%22middle%22 dominant-baseline=%22central%22%3ENo preview%3C/text%3E%3C/svg%3E" alt="Preview" style="max-width: 150px; max-height: 120px; object-fit: cover; border-radius: 0.5rem; display: none;">
                </div>
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
                <button type="submit" class="btn btn-primary" id="submit_btn">Create Tourist Spot</button>
                <a href="{{ route('admin.famous-tourist-spots.index') }}" class="btn btn-outline-secondary">Cancel</a>
            </div>

            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    const fileInput = document.getElementById('image_file');
                    const preview = document.getElementById('image_preview');
                    const fileInfo = document.getElementById('file-info');
                    const submitBtn = document.getElementById('submit_btn');
                    const form = fileInput.closest('form');
                    let isFormSubmitting = false;

                    if (fileInput) {
                        fileInput.addEventListener('change', function(e) {
                            const file = e.target.files && e.target.files[0];
                            if (!file) {
                                fileInfo.style.display = 'none';
                                preview.style.display = 'none';
                                return;
                            }

                            // Show file info
                            fileInfo.textContent = 'Selected: ' + file.name + ' (' + (file.size / 1024).toFixed(2) + ' KB)';
                            fileInfo.style.display = 'block';

                            // Show preview
                            const reader = new FileReader();
                            reader.onload = function(ev) {
                                try {
                                    preview.src = ev.target.result;
                                    preview.style.display = 'block';
                                } catch (inner) {
                                    console.error('Preview render failed', inner);
                                }
                            };
                            reader.onerror = function(err) {
                                console.error('FileReader error', err);
                                fileInfo.textContent = 'Error reading file';
                            };
                            reader.readAsDataURL(file);
                        });
                    }

                    if (form) {
                        form.addEventListener('submit', function(e) {
                            if (isFormSubmitting) {
                                e.preventDefault();
                                return false;
                            }
                            isFormSubmitting = true;
                            submitBtn.disabled = true;
                            submitBtn.textContent = 'Creating...';
                        });
                    }
                });
            </script>
        </form>
    </div>
</x-layout>
