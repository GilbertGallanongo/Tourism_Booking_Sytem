<x-layout>
    <div class="section">
        <h1 class="title">Famous Tourist Spots</h1>
        <p class="lead">Manage famous tourist spots visible to tourists and guests.</p>
    </div>

    <div class="card">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
            <h2 class="subtitle">All Spots</h2>
            <a href="{{ route('admin.famous-tourist-spots.create') }}" class="btn btn-primary">Add New Spot</a>
        </div>

        <div style="overflow-x: auto;">
            <table style="width: 100%; border-collapse: collapse; font-size: 0.9rem;">
                <thead>
                    <tr style="background: #1a1a2e; color: white;">
                        <th style="padding: 0.75rem; text-align: left; border-bottom: 2px solid #4CAF50;">Image</th>
                        <th style="padding: 0.75rem; text-align: left; border-bottom: 2px solid #4CAF50;">Name</th>
                        <th style="padding: 0.75rem; text-align: left; border-bottom: 2px solid #4CAF50;">Location</th>
                        <th style="padding: 0.75rem; text-align: left; border-bottom: 2px solid #4CAF50;">Status</th>
                        <th style="padding: 0.75rem; text-align: left; border-bottom: 2px solid #4CAF50;">Sort Order</th>
                        <th style="padding: 0.75rem; text-align: left; border-bottom: 2px solid #4CAF50;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($spots as $spot)
                        <tr style="border-bottom: 1px solid #3d3d5c;">
                            <td style="padding: 0.75rem;">
                                @if($spot->image)
                                    <img src="{{ asset('storage/' . $spot->image) }}" alt="{{ $spot->name }}" style="width: 60px; height: 60px; object-fit: cover; border-radius: 0.25rem;">
                                @else
                                    <div style="width: 60px; height: 60px; background: #3d3d5c; border-radius: 0.25rem; display: flex; align-items: center; justify-content: center; color: #8890a8; font-size: 0.75rem;">No Image</div>
                                @endif
                            </td>
                            <td style="padding: 0.75rem;">{{ $spot->name }}</td>
                            <td style="padding: 0.75rem;">{{ $spot->location }}</td>
                            <td style="padding: 0.75rem;">
                                <span style="padding: 0.25rem 0.5rem; border-radius: 0.25rem; font-size: 0.8rem; font-weight: 600;
                                    @if($spot->is_active) background: #28a745; color: white;
                                    @else background: #6c757d; color: white; @endif">
                                    {{ $spot->is_active ? 'Active' : 'Inactive' }}
                                </span>
                            </td>
                            <td style="padding: 0.75rem;">{{ $spot->sort_order }}</td>
                            <td style="padding: 0.75rem;">
                                <div style="display: flex; gap: 0.5rem;">
                                    <a href="{{ route('admin.famous-tourist-spots.edit', $spot) }}" class="btn btn-primary" style="padding: 0.25rem 0.5rem; font-size: 0.8rem;">Edit</a>
                                    <form action="{{ route('admin.famous-tourist-spots.destroy', $spot) }}" method="POST" style="display: inline;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-outline-secondary" style="padding: 0.25rem 0.5rem; font-size: 0.8rem;" onclick="return confirm('Are you sure you want to delete this tourist spot?')">Delete</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" style="padding: 2rem; text-align: center; color: #8890a8;">
                                No famous tourist spots found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($spots->hasPages())
            <div style="margin-top: 1.5rem;">
                {{ $spots->links() }}
            </div>
        @endif
    </div>
</x-layout>
