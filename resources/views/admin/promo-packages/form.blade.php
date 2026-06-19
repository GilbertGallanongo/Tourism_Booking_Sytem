<div class="card">
    <div class="card-body">
        @if($errors->any())
            <div class="alert alert-danger mb-4">
                <ul class="mb-0">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ $action }}" enctype="multipart/form-data">
            @csrf
            @if($method === 'PUT')
                @method('PUT')
            @endif

            <div class="row g-3 mb-3">
                <div class="col-12">
                    <label class="form-label">Promo Package Name</label>
                    <input type="text" name="name" value="{{ old('name', $promoPackage->name ?? '') }}" class="form-control" required>
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label">Description</label>
                <textarea name="description" rows="4" class="form-control">{{ old('description', $promoPackage->description ?? '') }}</textarea>
            </div>

            <div class="mb-3">
                <label class="form-label" for="image">Image</label>
                <input type="file" name="image" id="image" class="form-control @error('image') is-invalid @enderror" accept="image/*">
                <div id="file-name-display" class="form-text"></div>
                <div class="mt-2">
                    @php
                        $previewPath = $promoPackage->image ? $promoPackage->image_url : null;
                    @endphp
                    @if($previewPath)
                        <x-image-view-link :src="$previewPath" :title="$promoPackage->name ?: 'Promo image'" style="display:inline-block;">
                            <img id="image_preview" src="{{ $previewPath }}" alt="Preview" style="max-width: 160px; max-height: 120px; object-fit: cover; border-radius: 6px; display: inline-block;">
                        </x-image-view-link>
                    @else
                        <img id="image_preview" src="data:image/svg+xml,%3Csvg xmlns=%22http://www.w3.org/2000/svg%22 width=%22200%22 height=%22200%22 fill=%22%23e9ecef%22%3E%3Crect width=%22200%22 height=%22200%22/%3E%3Ctext x=%2250%25%22 y=%2250%25%22 font-size=%2214%22 fill=%22%23999%22 text-anchor=%22middle%22 dominant-baseline=%22central%22%3ENo preview%3C/text%3E%3C/svg%3E" alt="Preview" style="max-width: 160px; max-height: 120px; object-fit: cover; border-radius: 6px; display: none;">
                    @endif
                    @if($promoPackage->image)
                        <div class="form-text mt-2" id="current_image_info">
                            <strong>Current image:</strong> Leave blank to keep existing
                        </div>
                    @endif
                </div>
                @error('image')
                    <div class="invalid-feedback d-block">{{ $message }}</div>
                @enderror
            </div>

            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    const fileInput = document.getElementById('image');
                    const preview = document.getElementById('image_preview');
                    const fileNameDisplay = document.getElementById('file-name-display');
                    const currentImageInfo = document.getElementById('current_image_info');
                    const uploadUrl = @json($promoPackage->exists ? route('admin.promo-packages.upload-image', $promoPackage) : null);
                    let uploadInProgress = false;
                    window.promoImageProcessing = false;

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

                    function replaceSelectedFile(file) {
                        try {
                            const dataTransfer = new DataTransfer();
                            dataTransfer.items.add(file);
                            fileInput.files = dataTransfer.files;
                        } catch (error) {
                            console.warn('Could not replace selected image file', error);
                        }
                    }

                    function canvasToBlob(canvas, type, quality) {
                        return new Promise((resolve) => {
                            canvas.toBlob(resolve, type, quality);
                        });
                    }

                    async function preparePromoImage(file) {
                        const maxBytes = 1500 * 1024;
                        const maxSide = 1600;

                        if (!file.type.startsWith('image/') || file.type === 'image/gif' || file.size <= maxBytes) {
                            return file;
                        }

                        window.promoImageProcessing = true;
                        showToast('Preparing image...');

                        try {
                            const bitmap = await createImageBitmap(file);
                            const scale = Math.min(1, maxSide / Math.max(bitmap.width, bitmap.height));
                            const width = Math.max(1, Math.round(bitmap.width * scale));
                            const height = Math.max(1, Math.round(bitmap.height * scale));
                            const canvas = document.createElement('canvas');
                            canvas.width = width;
                            canvas.height = height;
                            const context = canvas.getContext('2d');
                            context.drawImage(bitmap, 0, 0, width, height);
                            bitmap.close();

                            let blob = null;
                            for (const quality of [0.82, 0.68, 0.55, 0.45]) {
                                blob = await canvasToBlob(canvas, 'image/jpeg', quality);
                                if (blob && blob.size <= maxBytes) {
                                    break;
                                }
                            }

                            if (!blob || blob.size >= file.size) {
                                return file;
                            }

                            const compressedName = file.name.replace(/\.[^.]+$/, '') + '.jpg';
                            return new File([blob], compressedName, {
                                type: 'image/jpeg',
                                lastModified: Date.now()
                            });
                        } catch (error) {
                            console.error('Image preparation failed', error);
                            return file;
                        } finally {
                            window.promoImageProcessing = false;
                            hideToast();
                        }
                    }

                    if (fileInput && preview) {
                        fileInput.addEventListener('change', async function(e) {
                            let file = e.target.files && e.target.files[0];
                            if (!file) {
                                fileNameDisplay.textContent = '';
                                return;
                            }

                            file = await preparePromoImage(file);
                            replaceSelectedFile(file);

                            fileNameDisplay.textContent = 'Selected: ' + file.name + ' (' + (file.size / 1024).toFixed(2) + ' KB)';

                            // Show preview immediately
                            const reader = new FileReader();
                            reader.onload = function(ev) {
                                try {
                                    preview.src = ev.target.result;
                                    preview.style.display = 'inline-block';
                                    preview.style.maxWidth = '160px';
                                    preview.style.maxHeight = '120px';
                                    preview.style.objectFit = 'cover';
                                    preview.style.borderRadius = '6px';
                                    if (currentImageInfo) {
                                        currentImageInfo.textContent = 'New image will replace current image on save';
                                    }
                                } catch (inner) {
                                    console.error('Preview render failed', inner);
                                }
                            };
                            reader.onerror = function(err) {
                                console.error('FileReader error', err);
                                fileNameDisplay.textContent = 'Error reading file';
                            };
                            reader.readAsDataURL(file);

                            // If editing and package exists, try async upload
                            if (uploadUrl && !uploadInProgress) {
                                uploadInProgress = true;
                                showToast('Uploading image...');

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
                                    uploadInProgress = false;
                                    if (data.url) {
                                        preview.src = data.url + '?v=' + (data.timestamp || Date.now());
                                        showToast('Image uploaded successfully');
                                        hideToast();
                                    } else {
                                        showToast('Note: Image will be saved when you submit the form');
                                        hideToast();
                                    }
                                })
                                .catch(err => {
                                    uploadInProgress = false;
                                    console.error('Upload error (non-critical)', err);
                                    showToast('Note: Image will be saved when you submit the form');
                                    hideToast();
                                });
                            }
                        });
                    }
                });
            </script>

            <div class="row g-3 mb-3">
                <div class="col-12 col-md-4">
                    <label class="form-label">Discount Percentage (%)</label>
                    <input type="number" name="discount_percentage" value="{{ old('discount_percentage', $promoPackage->discount_percentage ?? '') }}" class="form-control" min="0" max="100" step="0.01" required>
                </div>
                <div class="col-12 col-md-4">
                    <label class="form-label">Start Date</label>
                    <input type="date" name="start_date" value="{{ old('start_date', $promoPackage->start_date?->format('Y-m-d') ?? '') }}" class="form-control" required>
                </div>
                <div class="col-12 col-md-4">
                    <label class="form-label">End Date</label>
                    <input type="date" name="end_date" value="{{ old('end_date', $promoPackage->end_date?->format('Y-m-d') ?? '') }}" class="form-control" required>
                </div>
            </div>

            <div class="mb-3">
                <div class="form-check">
                    <input type="hidden" name="is_active" value="0">
                    <input type="checkbox" name="is_active" id="is_active" class="form-check-input" value="1" {{ old('is_active', $promoPackage->exists ? $promoPackage->is_active : true) ? 'checked' : '' }}>
                    <label class="form-check-label" for="is_active">Active</label>
                </div>
            </div>

            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary" id="submit_btn">{{ $button }}</button>
                <a href="{{ route('admin.promo-packages.index') }}" class="btn btn-outline-secondary">Cancel</a>
            </div>

            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    const form = document.querySelector('form');
                    const submitBtn = document.getElementById('submit_btn');
                    let isSubmitting = false;

                    if (form) {
                        form.addEventListener('submit', function(e) {
                            if (isSubmitting) {
                                e.preventDefault();
                                return false;
                            }

                            if (window.promoImageProcessing) {
                                e.preventDefault();
                                return false;
                            }

                            isSubmitting = true;
                            submitBtn.disabled = true;
                            submitBtn.textContent = submitBtn.textContent.includes('Create') ? 'Creating...' : 'Updating...';

                            // Show success message after submission
                            setTimeout(() => {
                                const successAlert = document.querySelector('.alert-success');
                                if (successAlert) {
                                    successAlert.style.display = 'block';
                                }
                            }, 500);
                        });
                    }

                    // Show error feedback if validation errors exist
                    const errorAlerts = document.querySelectorAll('.alert-danger');
                    if (errorAlerts.length > 0) {
                        errorAlerts.forEach(alert => {
                            alert.style.display = 'block';
                        });
                    }
                });
            </script>
        </form>
    </div>
</div>
