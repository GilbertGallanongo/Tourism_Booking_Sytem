<x-layout>
    <div class="section">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h1 class="title">{{ $package->name }}</h1>
                <p class="lead">{{ $package->location }}</p>
            </div>
            <div class="d-flex gap-2">
                <a href="{{ route('admin.packages.edit', $package) }}" class="btn btn-primary">Edit Package</a>
                <a href="{{ route('admin.packages.index') }}" class="btn btn-outline-secondary">Back</a>
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                <div class="row g-4">
                    <div class="col-12 col-md-5">
                        <img
                            src="{{ $package->image_url }}"
                            alt="{{ $package->name }}"
                            style="width:100%;max-height:320px;object-fit:cover;border-radius:8px;"
                        >
                    </div>
                    <div class="col-12 col-md-7">
                        <div class="tablewrap">
                            <table class="table">
                                <tbody>
                                    <tr>
                                        <th class="th">Price</th>
                                        <td class="td">PHP {{ number_format((float) $package->price, 2) }}</td>
                                    </tr>
                                    <tr>
                                        <th class="th">Duration</th>
                                        <td class="td">{{ $package->duration_days }} day{{ $package->duration_days === 1 ? '' : 's' }}</td>
                                    </tr>
                                    <tr>
                                        <th class="th">Max guests</th>
                                        <td class="td">{{ $package->max_guests }}</td>
                                    </tr>
                                    <tr>
                                        <th class="th">Status</th>
                                        <td class="td">{{ ucfirst($package->status) }}</td>
                                    </tr>
                                    <tr>
                                        <th class="th">Rating</th>
                                        <td class="td">{{ number_format((float) $package->rating, 1) }}</td>
                                    </tr>
                                    <tr>
                                        <th class="th">Image path</th>
                                        <td class="td">{{ $package->image ?: 'No image set' }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <div class="mt-4">
                            <h2 class="subtitle">Description</h2>
                            <p class="lead">{{ $package->description ?: 'No description provided.' }}</p>
                        </div>

                        <form method="POST" action="{{ route('admin.packages.destroy', $package) }}" class="mt-4" onsubmit="return confirm('Delete this package?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger">Delete Package</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-layout>
