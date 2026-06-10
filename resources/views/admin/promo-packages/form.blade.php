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
                <input type="file" name="image" id="image" class="form-control">
                <div id="file-name-display" class="form-text"></div>
                @if($promoPackage->image)
                    <div class="mt-2">
                        <img src="{{ asset('storage/' . $promoPackage->image) }}" alt="{{ $promoPackage->name }}" style="max-width: 200px; max-height: 200px;">
                        <div class="form-text">Current image (leave blank to keep existing image)</div>
                    </div>
                @endif
                @error('image')
                    <div class="invalid-feedback d-block">{{ $message }}</div>
                @enderror
            </div>

            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    const fileInput = document.getElementById('image');
                    const fileNameDisplay = document.getElementById('file-name-display');
                    
                    if (fileInput && fileNameDisplay) {
                        fileInput.addEventListener('change', function(e) {
                            if (this.files && this.files.length > 0) {
                                fileNameDisplay.textContent = 'Selected: ' + this.files[0].name;
                            } else {
                                fileNameDisplay.textContent = '';
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
                    <input type="checkbox" name="is_active" id="is_active" class="form-check-input" value="1" {{ old('is_active', $promoPackage->is_active ?? true) ? 'checked' : '' }}>
                    <label class="form-check-label" for="is_active">Active</label>
                </div>
            </div>

            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary">{{ $button }}</button>
                <a href="{{ route('admin.promo-packages.index') }}" class="btn btn-outline-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>
