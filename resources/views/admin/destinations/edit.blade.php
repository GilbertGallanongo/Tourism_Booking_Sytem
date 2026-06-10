<x-layout>
    <div class="section">
        <h1 class="title">Edit Destination</h1>
        <p class="lead">Update the destination details used by tour packages.</p>
    </div>

    @include('admin.destinations.form', [
        'destination' => $destination,
        'action' => route('admin.destinations.update', $destination),
        'method' => 'PUT',
        'button' => 'Update Destination',
    ])
</x-layout>
