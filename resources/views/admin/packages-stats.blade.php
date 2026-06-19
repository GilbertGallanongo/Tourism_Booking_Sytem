<x-layout>
    <div class="section">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h1 class="title">Package Management</h1>
                <p class="lead">View all packages and their status.</p>
            </div>
            <a href="{{ route('admin.packages.create') }}" class="btn btn-primary">Create Package</a>
        </div>
    </div>

    <div class="stats">
        <div class="card">
            <p>Total Packages</p>
            <strong class="value">{{ $stats['total'] }}</strong>
        </div>
        <div class="card">
            <p>Active</p>
            <strong class="value">{{ $stats['active'] }}</strong>
        </div>
        <div class="card">
            <p>Inactive</p>
            <strong class="value">{{ $stats['inactive'] }}</strong>
        </div>
    </div>

    <div class="section">
        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h2 class="subtitle">All Packages</h2>
                </div>
                <form action="{{ route('admin.packages-stats') }}" method="GET" class="packages-search-filter mb-4">
                    <div class="form-row">
                        <input
                            type="search"
                            name="search"
                            value="{{ request('search') }}"
                            placeholder="Search packages by name, location, or description"
                            class="form-control"
                        />

                        <select name="category" class="form-select">
                            <option value="">All categories</option>
                            @foreach($categories as $key => $label)
                                <option value="{{ $key }}" @selected(request('category') === $key)>{{ $label }}</option>
                            @endforeach
                        </select>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">Search</button>
                            @if(request('search') || request('category'))
                                <a href="{{ route('admin.packages-stats') }}" class="btn btn-outline-secondary">Clear</a>
                            @endif
                        </div>
                    </div>
                </form>
            </div>
            <div class="tablewrap">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th class="th">Image</th>
                            <th class="th">Name</th>
                            <th class="th">Location</th>
                            <th class="th">Category</th>
                            <th class="th">Price</th>
                            <th class="th">Status</th>
                            <th class="th">Rating</th>
                            <th class="th">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($packages as $package)
                            <tr>
                                <td class="td">
                                    <x-image-view-link :src="$package->image_url" :title="$package->name" style="display:inline-block;">
                                        <img src="{{ $package->image_url }}" alt="{{ $package->name }}" style="width:88px;height:56px;object-fit:cover;border-radius:6px;">
                                    </x-image-view-link>
                                </td>
                                <td class="td">{{ $package->name }}</td>
                                <td class="td">{{ $package->location }}</td>
                                <td class="td">{{ $package->category_label }}</td>
                                <td class="td">PHP {{ number_format((float) $package->price, 2) }}</td>
                                <td class="td">{{ ucfirst($package->status) }}</td>
                                <td class="td">{{ number_format((float) $package->rating, 1) }}</td>
                                <td class="td table-actions">
                                    <a href="{{ route('admin.packages.show', $package) }}" class="btn btn-sm btn-outline-secondary">View</a>
                                    <a href="{{ route('admin.packages.edit', $package) }}" class="btn btn-sm btn-outline-secondary">Edit</a>
                                    <form method="POST" action="{{ route('admin.packages.destroy', $package) }}" onsubmit="return confirm('Delete this package?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger">Delete</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="td empty">No packages found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="package-pagination mt-4">{{ $packages->links() }}</div>
        </div>
    </div>
</x-layout>
