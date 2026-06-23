<x-layout title="Edit Reservation">

@php
    $package = $booking->package;
    $promoPackage = $booking->promoPackage && $booking->promoPackage->isActive() ? $booking->promoPackage : null;
    $selectedServices = collect(old('services', $booking->services ?? []))
        ->map(fn ($service) => is_array($service) ? ($service['key'] ?? null) : $service)
        ->filter()
        ->values()
        ->all();
    $tourStart = old('tour_start_date', optional($booking->tour_start_date)->format('Y-m-d'));
    $tourEnd = old('tour_end_date', optional($booking->tour_end_date)->format('Y-m-d'));
    $children = (int) old('num_children', $booking->num_children ?? 0);
    $seniors = (int) old('num_seniors', $booking->num_seniors ?? 0);
    $guests = (int) old('num_guests', $booking->num_guests ?? 1);
@endphp

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-semibold mb-1">Edit Reservation</h4>
        <p class="text-muted mb-0">Booking #{{ $booking->booking_number }}</p>
    </div>
    <a href="{{ route('reservations.index') }}" class="btn btn-sm btn-outline-secondary">Back to reservations</a>
</div>

<div class="row g-4">
    <div class="col-lg-8">
        <div class="card p-4">
            <form method="POST" action="{{ route('reservations.update', $booking) }}">
                @csrf
                @method('PATCH')

                <div class="mb-4">
                    <h5 class="fw-semibold mb-1">{{ $package->name }}</h5>
                    <p class="text-muted mb-0">{{ $package->location }} &bull; {{ $package->duration_days }} day{{ $package->duration_days === 1 ? '' : 's' }}</p>
                </div>

                <div class="row g-3 mb-3">
                    <div class="col-sm-6">
                        <label class="form-label">Tour start</label>
                        <input type="date" name="tour_start_date"
                               class="form-control @error('tour_start_date') is-invalid @enderror"
                               value="{{ $tourStart }}"
                               min="{{ now()->format('Y-m-d') }}">
                        @error('tour_start_date')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-sm-6">
                        <label class="form-label">Tour end</label>
                        <input type="date" name="tour_end_date"
                               id="tour_end_date"
                               class="form-control @error('tour_end_date') is-invalid @enderror"
                               value="{{ $tourEnd }}">
                        <div class="form-text">Tour end follows this package duration.</div>
                        @error('tour_end_date')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label">Travelers</label>
                    <div class="row g-3">
                        <div class="col-sm-4">
                            <input type="number" name="num_guests" id="num_guests"
                                   class="form-control @error('num_guests') is-invalid @enderror"
                                   value="{{ $guests }}"
                                   min="1" max="{{ $package->max_guests }}">
                            <div class="form-text">Total travelers</div>
                            @error('num_guests')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-sm-4">
                            <input type="number" name="num_children" id="num_children"
                                   class="form-control @error('num_children') is-invalid @enderror"
                                   value="{{ $children }}"
                                   min="0" max="{{ $package->max_guests }}">
                            <div class="form-text">Children 7 years old and below</div>
                            @error('num_children')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-sm-4">
                            <input type="number" name="num_seniors" id="num_seniors"
                                   class="form-control @error('num_seniors') is-invalid @enderror"
                                   value="{{ $seniors }}"
                                   min="0" max="{{ $package->max_guests }}">
                            <div class="form-text">Seniors</div>
                            @error('num_seniors')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label">Tour guide</label>
                    <div class="form-check">
                        <input class="form-check-input booking-guide" type="checkbox"
                               name="tourist_guide" value="1"
                               id="tourist_guide" data-price="1200"
                               {{ old('tourist_guide', $booking->tourist_guide) ? 'checked' : '' }}>
                        <label class="form-check-label" for="tourist_guide">
                            Include a tourist guide <span class="text-muted">(&#8369;1,200)</span>
                        </label>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label">Optional add-ons</label>
                    <div class="form-check mb-2">
                        <input class="form-check-input booking-service" type="checkbox"
                               name="services[]" value="airport_transfer"
                               id="airport_transfer" data-price="1200"
                               {{ in_array('airport_transfer', $selectedServices, true) ? 'checked' : '' }}>
                        <label class="form-check-label" for="airport_transfer">
                            Airport transfer <span class="text-muted">(&#8369;1,200)</span>
                        </label>
                    </div>
                    <div class="form-check mb-2">
                        <input class="form-check-input booking-service" type="checkbox"
                               name="services[]" value="travel_insurance"
                               id="travel_insurance" data-price="450"
                               {{ in_array('travel_insurance', $selectedServices, true) ? 'checked' : '' }}>
                        <label class="form-check-label" for="travel_insurance">
                            Travel insurance <span class="text-muted">(&#8369;450)</span>
                        </label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input booking-service" type="checkbox"
                               name="services[]" value="meal_plan"
                               id="meal_plan" data-price="650"
                               {{ in_array('meal_plan', $selectedServices, true) ? 'checked' : '' }}>
                        <label class="form-check-label" for="meal_plan">
                            Meal plan <span class="text-muted">(&#8369;650)</span>
                        </label>
                    </div>
                    @error('services')
                        <div class="text-danger small mt-2">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-4">
                    <label class="form-label">Special requests <span class="text-muted">(optional)</span></label>
                    <textarea name="special_requests" rows="3"
                              class="form-control @error('special_requests') is-invalid @enderror"
                              placeholder="e.g. vegetarian meals, wheelchair access">{{ old('special_requests', $booking->special_requests) }}</textarea>
                    @error('special_requests')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="d-flex gap-2 flex-wrap">
                    <button type="submit" class="btn btn-primary">Save Changes</button>
                    <a href="{{ route('reservations.show', $booking) }}" class="btn btn-outline-secondary">Cancel</a>
                </div>
            </form>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card p-4">
            <h5 class="fw-semibold mb-3">Updated total</h5>
            <div class="d-flex justify-content-between mb-2">
                <span>Base rate</span>
                <strong id="base-rate-display">&#8369;{{ number_format($package->price, 2) }}</strong>
            </div>
            <div class="d-flex justify-content-between mb-2">
                <span>Guests</span>
                <strong id="guest-total">{{ $guests }}</strong>
            </div>
            <div class="d-flex justify-content-between mb-2" id="child-rate-row" style="display: none;">
                <span>Children @ 50%</span>
                <strong id="child-total">&#8369;0.00</strong>
            </div>
            <div class="d-flex justify-content-between mb-2" id="senior-rate-row" style="display: none;">
                <span>Seniors @ 80%</span>
                <strong id="senior-total">&#8369;0.00</strong>
            </div>
            <div class="d-flex justify-content-between mb-2">
                <span>Extras</span>
                <strong id="extras-total">&#8369;0.00</strong>
            </div>
            @if($promoPackage)
                <div class="d-flex justify-content-between mb-2">
                    <span>{{ $promoPackage->name }} discount</span>
                    <strong id="discount-total">-&#8369;0.00</strong>
                </div>
            @endif
            <div class="d-flex justify-content-between mb-2" id="guide-fee-row" style="display: none;">
                <span>Tour guide fee</span>
                <strong id="guide-fee-total">&#8369;0.00</strong>
            </div>
            <hr>
            <div class="d-flex justify-content-between fw-semibold">
                <span>Total estimated</span>
                <strong id="total-display">&#8369;{{ number_format($booking->total_price, 2) }}</strong>
            </div>
            <p class="text-muted small mt-3 mb-0">Only pending reservations can be edited. Changes will update the reservation total.</p>
        </div>
    </div>
</div>

<script>
    const basePrice = {{ $package->price }};
    const promoDiscount = {{ $promoPackage ? $promoPackage->discount_percentage : 0 }};
    const maxGuests = {{ $package->max_guests }};
    const bookingDurationDays = {{ $package->duration_days }};
    const startInput = document.querySelector('input[name="tour_start_date"]');
    const endInput = document.getElementById('tour_end_date');
    const totalTravelersInput = document.getElementById('num_guests');
    const childInput = document.getElementById('num_children');
    const seniorInput = document.getElementById('num_seniors');
    const serviceCheckboxes = document.querySelectorAll('.booking-service');
    const guideCheckbox = document.querySelector('input[name="tourist_guide"]');
    const guestTotalDisplay = document.getElementById('guest-total');
    const extrasTotalDisplay = document.getElementById('extras-total');
    const discountTotalDisplay = document.getElementById('discount-total');
    const guideFeeRow = document.getElementById('guide-fee-row');
    const guideFeeDisplay = document.getElementById('guide-fee-total');
    const totalDisplay = document.getElementById('total-display');
    const childTotalDisplay = document.getElementById('child-total');
    const seniorTotalDisplay = document.getElementById('senior-total');
    const childRateRow = document.getElementById('child-rate-row');
    const seniorRateRow = document.getElementById('senior-rate-row');

    const formatMoney = (value) => {
        return '\u20b1' + value.toLocaleString('en-PH', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
    };

    const calculateTotals = () => {
        const totalTravelers = Math.min(Math.max(parseInt(totalTravelersInput.value) || 1, 1), maxGuests);
        const children = Math.min(Math.max(parseInt(childInput.value) || 0, 0), totalTravelers);
        const seniors = Math.min(Math.max(parseInt(seniorInput.value) || 0, 0), totalTravelers);
        const adjustedChildren = children + seniors > totalTravelers ? Math.max(0, totalTravelers - seniors) : children;
        const adults = Math.max(0, totalTravelers - adjustedChildren - seniors);

        totalTravelersInput.value = totalTravelers;
        childInput.value = adjustedChildren;
        seniorInput.value = seniors;
        guestTotalDisplay.textContent = totalTravelers;

        let serviceTotal = 0;
        serviceCheckboxes.forEach((checkbox) => {
            if (checkbox.checked) {
                serviceTotal += parseFloat(checkbox.dataset.price) || 0;
            }
        });

        const guideFee = guideCheckbox && guideCheckbox.checked ? parseFloat(guideCheckbox.dataset.price) || 0 : 0;
        const childAmount = adjustedChildren * (basePrice * 0.5);
        const seniorAmount = seniors * (basePrice * 0.8);
        const adultAmount = adults * basePrice;
        const baseAmount = adultAmount + childAmount + seniorAmount;
        const discountAmount = promoDiscount > 0 ? baseAmount * (promoDiscount / 100) : 0;
        const total = baseAmount - discountAmount + serviceTotal + guideFee;

        childTotalDisplay.textContent = formatMoney(childAmount);
        seniorTotalDisplay.textContent = formatMoney(seniorAmount);
        childRateRow.style.display = adjustedChildren > 0 ? 'flex' : 'none';
        seniorRateRow.style.display = seniors > 0 ? 'flex' : 'none';
        extrasTotalDisplay.textContent = formatMoney(serviceTotal);
        if (discountTotalDisplay) {
            discountTotalDisplay.textContent = '-' + formatMoney(discountAmount);
        }
        guideFeeDisplay.textContent = formatMoney(guideFee);
        guideFeeRow.style.display = guideFee > 0 ? 'flex' : 'none';
        totalDisplay.textContent = formatMoney(total);
    };

    const updateEndDate = () => {
        if (!startInput || !endInput || !startInput.value) {
            return;
        }

        const date = new Date(startInput.value);
        date.setDate(date.getDate() + bookingDurationDays);
        const formatted = date.toISOString().slice(0, 10);
        endInput.min = formatted;
        endInput.max = formatted;
        endInput.value = formatted;
    };

    [totalTravelersInput, childInput, seniorInput].forEach((input) => input.addEventListener('input', calculateTotals));
    serviceCheckboxes.forEach((checkbox) => checkbox.addEventListener('change', calculateTotals));
    if (guideCheckbox) {
        guideCheckbox.addEventListener('change', calculateTotals);
    }
    if (startInput) {
        startInput.addEventListener('change', updateEndDate);
    }

    updateEndDate();
    calculateTotals();
</script>

</x-layout>
