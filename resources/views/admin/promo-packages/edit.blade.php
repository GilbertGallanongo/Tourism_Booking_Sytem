<x-layout>
    <div class="section">
        <h1 class="title">Edit Promo Package</h1>
        <p class="lead">Update the promotional package details and discount.</p>
    </div>

    @include('admin.promo-packages.form', [
        'promoPackage' => $promoPackage,
        'action' => route('admin.promo-packages.update', $promoPackage),
        'method' => 'PUT',
        'button' => 'Update Promo Package',
    ])
</x-layout>
