<x-layout>
    <div class="section mb-4">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h1 class="title">Promo Packages</h1>
                <p class="lead">Manage promotional packages and discounts for tour bookings.</p>
            </div>
            <a href="{{ route('admin.promo-packages.create') }}" class="btn btn-primary">Add Promo Package</a>
        </div>
    </div>

    @if($promoPackages->isEmpty())
        <div class="card empty">No promo packages available.</div>
    @else
        <div class="card">
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Image</th>
                            <th>Name</th>
                            <th>Discount</th>
                            <th>Start Date</th>
                            <th>End Date</th>
                            <th>Status</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($promoPackages as $promoPackage)
                            <tr>
                                <td>
                                    @if($promoPackage->image)
                                        <img src="{{ $promoPackage->image_url }}" alt="{{ $promoPackage->name }}" style="width: 50px; height: 50px; object-fit: cover; border-radius: 4px;">
                                    @else
                                        <span style="color: #8890a8; font-size: 0.875rem;">No image</span>
                                    @endif
                                </td>
                                <td>{{ $promoPackage->name }}</td>
                                <td>{{ $promoPackage->discount_percentage }}%</td>
                                <td>{{ $promoPackage->start_date->format('M d, Y') }}</td>
                                <td>{{ $promoPackage->end_date->format('M d, Y') }}</td>
                                <td>
                                    @if($promoPackage->is_active)
                                        <span class="badge bg-success">Active</span>
                                    @else
                                        <span class="badge bg-secondary">Inactive</span>
                                    @endif
                                </td>
                                <td class="text-end">
                                    <a href="{{ route('admin.promo-packages.edit', $promoPackage) }}" class="btn btn-sm btn-outline-secondary">Edit</a>
                                    <form action="{{ route('admin.promo-packages.destroy', $promoPackage) }}" method="POST" class="d-inline-block">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Delete this promo package?')">Delete</button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <div class="mt-4">{{ $promoPackages->links() }}</div>
    @endif
</x-layout>
