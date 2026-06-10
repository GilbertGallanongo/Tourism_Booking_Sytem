<x-layout>
    <div class="section">
        <h1 class="title">Create Tour Package</h1>
        <p class="lead">Add a new Bolinao tour package for customers to book.</p>
    </div>

    @include('admin.packages.form', [
        'package' => $package,
        'action' => route('admin.packages.store'),
        'method' => 'POST',
        'button' => 'Save Package',
    ])
</x-layout>
