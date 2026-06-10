<x-layout>
    <div class="section">
        <h1 class="title">Create Promo Package</h1>
        <p class="lead">Add a new promotional package with discount for tour bookings.</p>
    </div>

    @include('admin.promo-packages.form', [
        'promoPackage' => $promoPackage,
        'action' => route('admin.promo-packages.store'),
        'method' => 'POST',
        'button' => 'Save Promo Package',
    ])
</x-layout>
