<x-layout>
    <div class="section">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h1 class="title">Tour Packages</h1>
                <p class="lead">Manage your Bolinao tour packages here.</p>
            </div>
            <a href="{{ route('admin.packages.create') }}" class="btn btn-primary">Add Package</a>
        </div>

        @if(session('success'))
            <div class="alert alert-success mb-4">{{ session('success') }}</div>
        @endif

        <div class="card mb-4">
            <div class="card-body">
                <form action="{{ route('admin.packages.index') }}" method="GET" class="packages-search-filter">
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
                                <a href="{{ route('admin.packages.index') }}" class="btn btn-outline-secondary">Clear</a>
                            @endif
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <div class="card">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
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
                                    <img src="{{ $package->image_url }}" alt="{{ $package->name }}" style="width:88px;height:56px;object-fit:cover;border-radius:6px;">
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
                                <td colspan="8" class="td empty">No tour packages found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="package-pagination mt-3">{{ $packages->links() }}</div>
    </div>
</x-layout>
