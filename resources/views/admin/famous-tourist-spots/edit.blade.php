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
                    <img src="{{ $famousTouristSpot->image_url }}" alt="{{ $famousTouristSpot->name }}" style="width: 150px; height: 150px; object-fit: cover; border-radius: 0.5rem; margin-bottom: 0.5rem;">
                @else
                    <div style="width: 150px; height: 150px; background: #3d3d5c; border-radius: 0.5rem; display: flex; align-items: center; justify-content: center; color: #8890a8; margin-bottom: 0.5rem;">No Image</div>
                @endif
            </div>

            <div style="margin-bottom: 1.5rem;">
                <label style="display: block; margin-bottom: 0.5rem; font-weight: 600; color: white;">New Image</label>
                <input type="file" name="image" id="image_file" accept="image/jpeg,image/png,image/jpg,image/gif,image/webp" style="width: 100%; padding: 0.75rem; border: 1px solid #3d3d5c; border-radius: 0.5rem; background: #1a1a2e; color: white;">
                <div style="font-size: 0.8rem; color: #8890a8; margin-top: 0.25rem;">Leave empty to keep current image. Maximum file size: 5MB. Allowed formats: JPEG, PNG, JPG, GIF, WebP</div>
                <div id="file-info" style="font-size: 0.85rem; color: #4CAF50; margin-top: 0.5rem; display: none;"></div>
                <div style="margin-top: 0.75rem;">
                    <img id="new_image_preview" src="data:image/svg+xml,%3Csvg xmlns=%22http://www.w3.org/2000/svg%22 width=%22150%22 height=%22120%22 fill=%22%232a2a4a%22%3E%3Crect width=%22150%22 height=%22120%22/%3E%3Ctext x=%2250%25%22 y=%2250%25%22 font-size=%2212%22 fill=%22%23888%22 text-anchor=%22middle%22 dominant-baseline=%22central%22%3ENo preview%3C/text%3E%3C/svg%3E" alt="New Preview" style="max-width: 150px; max-height: 120px; object-fit: cover; border-radius: 0.5rem; display: none;">
                </div>
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
                <button type="submit" class="btn btn-primary" id="submit_btn">Update Tourist Spot</button>
                <a href="{{ route('admin.famous-tourist-spots.index') }}" class="btn btn-outline-secondary">Cancel</a>
            </div>

            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    const fileInput = document.getElementById('image_file');
                    const preview = document.getElementById('new_image_preview');
                    const fileInfo = document.getElementById('file-info');
                    const uploadUrl = @json(route('admin.famous-tourist-spots.upload-image', $famousTouristSpot));
                    const submitBtn = document.getElementById('submit_btn');
                    const form = fileInput.closest('form');
                    let isFormSubmitting = false;

                    // Toast notification
                    const toast = document.createElement('div');
                    toast.id = 'upload_toast';
                    toast.role = 'status';
                    toast.setAttribute('aria-live', 'polite');
                    toast.style.cssText = 'position:fixed;right:20px;top:20px;display:none;z-index:1100;background:rgba(0,0,0,0.85);color:#fff;padding:10px 14px;border-radius:6px;box-shadow:0 6px 18px rgba(0,0,0,0.2);font-size:13px;';
                    document.body.appendChild(toast);

                    function showToast(message) {
                        toast.textContent = message;
                        toast.style.display = 'block';
                        toast.style.opacity = '1';
                    }

                    function hideToast() {
                        setTimeout(() => {
                            toast.style.transition = 'opacity 300ms ease';
                            toast.style.opacity = '0';
                            setTimeout(() => {
                                toast.style.display = 'none';
                                toast.style.transition = '';
                            }, 300);
                        }, 2000);
                    }

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

                            // Try async upload (non-critical)
                            const fd = new FormData();
                            fd.append('_token', document.querySelector('input[name="_token"]').value);
                            fd.append('image', file);

                            fetch(uploadUrl, {
                                method: 'POST',
                                body: fd,
                                credentials: 'same-origin'
                            })
                            .then(res => res.json())
                            .then(data => {
                                if (data.url) {
                                    preview.src = data.url + '?v=' + (data.timestamp || Date.now());
                                    showToast('✓ Image uploaded');
                                    hideToast();
                                }
                            })
                            .catch(() => {
                                // Silently fail - image will be saved on form submit
                            });
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
                            submitBtn.textContent = 'Updating...';
                        });
                    }
                });
            </script>
        </form>
    </div>
</x-layout>
