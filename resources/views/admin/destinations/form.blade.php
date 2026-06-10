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

        <form method="POST" action="{{ $action }}">
            @csrf
            @if($method === 'PUT')
                @method('PUT')
            @endif

            <div class="row g-3 mb-3">
                <div class="col-12 col-md-6">
                    <label class="form-label">Destination name</label>
                    <input type="text" name="name" value="{{ old('name', $destination->name) }}" class="form-control">
                </div>
                <div class="col-12 col-md-6">
                    <label class="form-label">Location</label>
                    <input type="text" name="location" value="{{ old('location', $destination->location) }}" class="form-control">
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label">Description</label>
                <textarea name="description" rows="4" class="form-control">{{ old('description', $destination->description) }}</textarea>
            </div>

            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary">{{ $button }}</button>
                <a href="{{ route('admin.destinations.index') }}" class="btn btn-outline-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>
