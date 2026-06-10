<x-layout>
    <div class="section mb-4">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h1 class="title">Destinations</h1>
                <p class="lead">Manage the destination list used for tour packages.</p>
            </div>
            <a href="{{ route('admin.destinations.create') }}" class="btn btn-primary">Add Destination</a>
        </div>
    </div>

    @if($destinations->isEmpty())
        <div class="card empty">No destinations available.</div>
    @else
        <div class="card">
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Location</th>
                            <th>Description</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($destinations as $destination)
                            <tr>
                                <td>{{ $destination->name }}</td>
                                <td>{{ $destination->location }}</td>
                                <td>{{ \Illuminate\Support\Str::limit($destination->description, 80) }}</td>
                                <td class="text-end">
                                    <a href="{{ route('admin.destinations.edit', $destination) }}" class="btn btn-sm btn-outline-secondary">Edit</a>
                                    <form action="{{ route('admin.destinations.destroy', $destination) }}" method="POST" class="d-inline-block">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Delete this destination?')">Delete</button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <div class="mt-4">{{ $destinations->links() }}</div>
    @endif
</x-layout>
