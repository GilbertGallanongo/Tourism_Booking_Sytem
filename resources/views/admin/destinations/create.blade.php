<x-layout>
    <div class="section">
        <h1 class="title">Create Destination</h1>
        <p class="lead">Add a new destination so tour packages can be grouped and managed more easily.</p>
    </div>

    @include('admin.destinations.form', [
        'destination' => $destination,
        'action' => route('admin.destinations.store'),
        'method' => 'POST',
        'button' => 'Save Destination',
    ])
</x-layout>
