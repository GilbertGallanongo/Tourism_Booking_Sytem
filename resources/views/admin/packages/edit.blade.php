<x-layout>
    <div class="section">
        <h1 class="title">Edit Tour Package</h1>
        <p class="lead">Update the package details for this Bolinao tour.</p>
    </div>

    @include('admin.packages.form', [
        'package' => $package,
        'action' => route('admin.packages.update', $package),
        'method' => 'PUT',
        'button' => 'Update Package',
    ])
</x-layout>
