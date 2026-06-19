<x-layout title="Book a Tour">

@php
    $selectedPromoId = request('promo');
    $promo = $selectedPromoId ? \App\Models\PromoPackage::find($selectedPromoId) : null;
    $selectedPromo = $promo && $promo->isActive() ? $promo : null;
    $promoMinStartDays = $selectedPromo?->minStartDays() ?? 0;
    $promoStartMinDate = $promoMinStartDays > 0 ? now()->addDays($promoMinStartDays)->format('Y-m-d') : now()->format('Y-m-d');
@endphp

<section class="package-detail-page">
    <div class="package-detail-hero">
        <div class="package-detail-grid">
            <div class="package-detail-media">
                @if($tourPackage->image)
                    <x-image-view-link :src="$tourPackage->image_url" :title="$tourPackage->name" style="display:block;">
                        <img src="{{ $tourPackage->image_url }}" alt="{{ $tourPackage->name }}">
                    </x-image-view-link>
                @else
                    <div class="package-detail-placeholder" aria-hidden="true">Bolinao</div>
                @endif
                <span class="package-detail-badge">{{ $tourPackage->category_label }}</span>
            </div>

            <aside class="package-detail-summary">
                <p class="package-detail-kicker">Reserve your tour</p>
                <h1>{{ $tourPackage->name }}</h1>

                <div class="package-detail-rating" aria-label="Rated {{ number_format($tourPackage->average_rating, 1) }} out of 5">
                    @for($i = 1; $i <= 5; $i++)
                        <span>{!! $i <= round($tourPackage->average_rating) ? '&#9733;' : '&#9734;' !!}</span>
                    @endfor
                    <strong>{{ number_format($tourPackage->average_rating, 1) }}</strong>
                </div>

                <p class="package-detail-location">
                    {{ $tourPackage->location }} &nbsp;&bull;&nbsp; {{ $tourPackage->duration_days }} day{{ $tourPackage->duration_days === 1 ? '' : 's' }} &nbsp;&bull;&nbsp; Up to {{ $tourPackage->max_guests }} guests
                </p>

                <p class="package-detail-description">{{ $tourPackage->description }}</p>

                <div class="package-detail-price">
                    <span>Price per person</span>
                    @if($selectedPromo && $selectedPromo->isActive())
                        <strong>₱{{ number_format($selectedPromo->discountedPrice($tourPackage->price), 2) }}</strong>
                        <span style="display:block; font-size:0.88rem; color: rgba(255,255,255,0.7); text-decoration: line-through;">₱{{ number_format($tourPackage->price, 2) }}</span>
                        <div class="mt-2">
                            <span class="badge bg-success">{{ $selectedPromo->name }} - {{ $selectedPromo->discount_percentage }}% OFF</span>
                        </div>
                    @else
                        <strong>₱{{ number_format($tourPackage->price, 2) }}</strong>
                    @endif
                </div>

                <div class="package-detail-actions">
                    <a href="{{ route('packages.show', $tourPackage) }}" class="package-detail-primary">View package details</a>
                    <p>Reservation requests are reviewed by the tour operator and confirmed shortly after submission.</p>
                </div>
            </aside>
        </div>
    </div>

    <div class="row gx-4 gy-4 package-detail-content">
        <div class="col-lg-7">
            <section class="package-detail-panel">
                <div class="package-detail-section-heading">
                    <p>Traveler information</p>
                    <h2>Complete your reservation</h2>
                </div>

                @if(auth()->user()?->isGuest())
                    <div class="alert alert-warning">
                        Guest accounts can only browse tours. Create a tourist account or sign in to complete a booking.
                    </div>

                    <div class="d-flex gap-2 mb-4 flex-wrap">
                        <a href="#" class="package-detail-primary flex-grow-1" data-auth-open data-auth-mode="register">Create Tourist Account</a>
                        <a href="#" class="package-detail-primary flex-grow-1" data-auth-open data-auth-mode="signin">Sign In</a>
                    </div>
                @else
                    <form method="POST" action="{{ route('bookings.store') }}" class="package-detail-form-grid">
                        @csrf
                        <div class="package-detail-note mb-4">
                            <p class="mb-2 text-muted">Reservation note</p>
                            <p class="mb-0">Your request will be reviewed by the tour operator. We will contact you if there are any changes before confirmation.</p>
                        </div>
                        <input type="hidden" name="tour_package_id" value="{{ $tourPackage->id }}">
                        @if($selectedPromo && $selectedPromo->isActive())
                            <input type="hidden" name="promo_package_id" value="{{ $selectedPromo->id }}">
                        @endif

                        <div class="mb-3">
                            <label class="form-label">Primary contact</label>
                            <input type="text" name="guest_name"
                                   class="form-control @error('guest_name') is-invalid @enderror"
                                   value="{{ old('guest_name', auth()->user()->name) }}"
                                   placeholder="Name of booking contact">
                            @error('guest_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row g-3 mb-3">
                            <div class="col-sm-6">
                                <label class="form-label">Email</label>
                                <input type="email" name="guest_email"
                                       class="form-control @error('guest_email') is-invalid @enderror"
                                       value="{{ old('guest_email', auth()->user()->email) }}"
                                       placeholder="contact@example.com">
                                @error('guest_email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-sm-6">
                                <label class="form-label">Phone</label>
                                <input type="tel" name="guest_phone"
                                       class="form-control @error('guest_phone') is-invalid @enderror"
                                       value="{{ old('guest_phone', auth()->user()->phone ?? '') }}"
                                       placeholder="0917 123 4567">
                                @error('guest_phone')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row g-3 mb-3">
                            <div class="col-sm-6">
                                <label class="form-label">Tour start</label>
                                <input type="date" name="tour_start_date"
                                       class="form-control @error('tour_start_date') is-invalid @enderror"
                                       value="{{ old('tour_start_date', $promoStartMinDate) }}"
                                       min="{{ $promoStartMinDate }}">
                                @if($promoMinStartDays > 0)
                                    <div class="form-text text-muted">This promo requires tour start at least {{ $promoMinStartDays }} days from today.</div>
                                @endif
                                @error('tour_start_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-sm-6">
                                <label class="form-label">Tour end</label>
                                <input type="date" name="tour_end_date"
                                       id="tour_end_date"
                                       class="form-control @error('tour_end_date') is-invalid @enderror"
                                       value="{{ old('tour_end_date', \Carbon\Carbon::parse(old('tour_start_date', now()))->addDays($tourPackage->duration_days)->format('Y-m-d')) }}"
                                       min="{{ \Carbon\Carbon::parse(old('tour_start_date', now()))->addDays($tourPackage->duration_days)->format('Y-m-d') }}"
                                       max="{{ \Carbon\Carbon::parse(old('tour_start_date', now()))->addDays($tourPackage->duration_days)->format('Y-m-d') }}">
                                <div class="form-text text-muted">This package is {{ $tourPackage->duration_days }} day(s); tour end is fixed to match the duration.</div>
                                @error('tour_end_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Travelers</label>
                            <div class="row g-3">
                                <div class="col-sm-3">
                                    <input type="number" name="num_guests" id="num_guests"
                                           class="form-control @error('num_guests') is-invalid @enderror"
                                           value="{{ old('num_guests', 1) }}"
                                           min="1" max="{{ $tourPackage->max_guests }}"
                                           placeholder="Total travelers">
                                    <div class="form-text">Total travelers</div>
                                    @error('num_guests')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-sm-3">
                                    <input type="number" name="num_children" id="num_children"
                                           class="form-control @error('num_children') is-invalid @enderror"
                                           value="{{ old('num_children', 0) }}"
                                           min="0" max="{{ $tourPackage->max_guests }}"
                                           placeholder="Children 7 years old and below">
                                    <div class="form-text">Children 7 years old and below</div>
                                    @error('num_children')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-sm-3">
                                    <input type="number" name="num_seniors" id="num_seniors"
                                           class="form-control @error('num_seniors') is-invalid @enderror"
                                           value="{{ old('num_seniors', 0) }}"
                                           min="0" max="{{ $tourPackage->max_guests }}"
                                           placeholder="Seniors">
                                    <div class="form-text">Seniors</div>
                                    @error('num_seniors')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <input type="hidden" name="num_adults" id="num_adults" value="{{ max(0, intval(old('num_guests', 1)) - intval(old('num_children', 0)) - intval(old('num_seniors', 0))) }}">
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Tour guide</label>
                            <div class="form-check">
                                <input class="form-check-input booking-guide" type="checkbox"
                                       name="tourist_guide" value="1"
                                       id="tourist_guide" data-price="1200"
                                       {{ old('tourist_guide') ? 'checked' : '' }}>
                                <label class="form-check-label" for="tourist_guide">
                                    Include a tourist guide <span class="text-muted">(₱1,200)</span>
                                </label>
                            </div>
                            @error('tourist_guide')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Optional add-ons</label>
                            <div class="form-check mb-2">
                                <input class="form-check-input booking-service" type="checkbox"
                                       name="services[]" value="airport_transfer"
                                       id="airport_transfer" data-price="1200"
                                       {{ in_array('airport_transfer', old('services', [])) ? 'checked' : '' }}>
                                <label class="form-check-label" for="airport_transfer">
                                    Airport transfer <span class="text-muted">(₱1,200)</span>
                                </label>
                            </div>
                            <div class="form-check mb-2">
                                <input class="form-check-input booking-service" type="checkbox"
                                       name="services[]" value="travel_insurance"
                                       id="travel_insurance" data-price="450"
                                       {{ in_array('travel_insurance', old('services', [])) ? 'checked' : '' }}>
                                <label class="form-check-label" for="travel_insurance">
                                    Travel insurance <span class="text-muted">(₱450)</span>
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input booking-service" type="checkbox"
                                       name="services[]" value="meal_plan"
                                       id="meal_plan" data-price="650"
                                       {{ in_array('meal_plan', old('services', [])) ? 'checked' : '' }}>
                                <label class="form-check-label" for="meal_plan">
                                    Meal plan <span class="text-muted">(₱650)</span>
                                </label>
                            </div>
                        </div>

                        @error('services')
                            <div class="alert alert-danger">{{ $message }}</div>
                        @enderror

                        <div class="mb-3">
                            <label class="form-label">Special requests <span class="text-muted">(optional)</span></label>
                            <textarea name="special_requests" rows="3"
                                      class="form-control @error('special_requests') is-invalid @enderror"
                                      placeholder="e.g. vegetarian meals, wheelchair access">{{ old('special_requests') }}</textarea>
                            @error('special_requests')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-flex flex-column gap-3">
                            <button type="submit" class="package-detail-primary">Submit Reservation</button>
                            <a href="{{ route('packages.show', $tourPackage) }}" class="text-muted" style="text-decoration: underline;">Back to package</a>
                        </div>
                    </form>
                @endif
            </section>
        </div>

        <div class="col-lg-5">
            <section class="package-detail-panel">
                <div class="package-detail-section-heading">
                    <p>Package details</p>
                    <h2>Your tour summary</h2>
                </div>

                <div class="package-detail-stats">
                    <div>
                        <span>Destination</span>
                        <strong>{{ $tourPackage->destination?->name ?? 'Bolinao' }}</strong>
                    </div>
                    <div>
                        <span>Duration</span>
                        <strong>{{ $tourPackage->duration_days }} day{{ $tourPackage->duration_days === 1 ? '' : 's' }}</strong>
                    </div>
                    <div>
                        <span>Time</span>
                        <strong>
                            @if($tourPackage->time_start || $tourPackage->time_end)
                                {{ $tourPackage->time_start_formatted }}
                                @if($tourPackage->time_end)
                                    — {{ $tourPackage->time_end_formatted }}
                                @endif
                            @else
                                TBD
                            @endif
                        </strong>
                    </div>
                    <div>
                        <span>Max guests</span>
                        <strong>{{ $tourPackage->max_guests }}</strong>
                    </div>
                    <div>
                        <span>Category</span>
                        <strong>{{ $tourPackage->category_label }}</strong>
                    </div>
                </div>

                <div class="mt-4">
                    <h6 class="mb-2">What’s included</h6>
                    <ul class="small mb-0 text-muted">
                        <li>Fun Activities</li>
                        <li>Pre-approved itinerary for easy planning</li>
                        <li>Booking confirmation and customer support</li>
                        <li>Secure checkout with reservation tracking</li>
                    </ul>
                </div>
            </section>

            <section class="package-detail-panel">
                <div class="package-detail-section-heading">
                    <p>Estimated cost</p>
                    <h2>Booking total</h2>
                </div>

                <div class="d-flex justify-content-between mb-2">
                    <span>Base rate</span>
                    <strong id="base-rate-display">₱{{ number_format($selectedPromo && $selectedPromo->isActive() ? $selectedPromo->discountedPrice($tourPackage->price) : $tourPackage->price, 2) }}</strong>
                </div>
                @if($selectedPromo && $selectedPromo->isActive())
                    <div class="d-flex justify-content-between mb-2">
                        <span>Promo savings</span>
                        <strong id="discount-total">₱0.00</strong>
                    </div>
                @endif
                <div class="d-flex justify-content-between mb-2">
                    <span>Guests</span>
                    <strong id="guest-total">{{ old('num_guests', 1) }}</strong>
                </div>
                <div class="d-flex justify-content-between mb-2" id="child-rate-row" style="display: none;">
                    <span>Children 7 years old and below @ 50%</span>
                    <strong id="child-total">₱0.00</strong>
                </div>
                <div class="d-flex justify-content-between mb-2" id="senior-rate-row" style="display: none;">
                    <span>Seniors @ 80%</span>
                    <strong id="senior-total">₱0.00</strong>
                </div>
                <div class="d-flex justify-content-between mb-2">
                    <span>Extras</span>
                    <strong id="extras-total">₱0.00</strong>
                </div>
                <div class="d-flex justify-content-between mb-2" id="guide-fee-row" style="display: none;">
                    <span>Tour guide fee</span>
                    <strong id="guide-fee-total">₱0.00</strong>
                </div>
                <hr>
                <div class="d-flex justify-content-between fw-semibold">
                    <span>Total estimated</span>
                    <strong id="total-display">₱{{ number_format($selectedPromo && $selectedPromo->isActive() ? $selectedPromo->discountedPrice($tourPackage->price) : $tourPackage->price, 2) }}</strong>
                </div>

                <div class="alert alert-info mt-4 mb-0">
                    <strong>Note:</strong> This is an estimate. Final charges may vary after tour operator confirmation.
                </div>
            </section>
        </div>
    </div>
</section>

<script>
    const basePrice = {{ $tourPackage->price }};
    const promoDiscount = {{ $selectedPromo && $selectedPromo->isActive() ? $selectedPromo->discount_percentage : 0 }};
    const maxGuests = {{ $tourPackage->max_guests }};
    const bookingDurationDays = {{ $tourPackage->duration_days }};
    const checkInInput = document.querySelector('input[name="tour_start_date"]');
    const checkOutInput = document.getElementById('tour_end_date');
    const totalTravelersInput = document.getElementById('num_guests');
    const childInput = document.getElementById('num_children');
    const seniorInput = document.getElementById('num_seniors');
    const hiddenAdultsInput = document.getElementById('num_adults');
    const guestInputs = [
        totalTravelersInput,
        childInput,
        seniorInput,
    ];
    const serviceCheckboxes = document.querySelectorAll('.booking-service');
    const guideCheckbox = document.querySelector('input[name="tourist_guide"]');
    const guestTotalDisplay = document.getElementById('guest-total');
    const extrasTotalDisplay = document.getElementById('extras-total');
    const guideFeeRow = document.getElementById('guide-fee-row');
    const guideFeeDisplay = document.getElementById('guide-fee-total');
    const totalDisplay = document.getElementById('total-display');
    const baseRateDisplay = document.getElementById('base-rate-display');
    const discountTotalDisplay = document.getElementById('discount-total');
    const childTotalDisplay = document.getElementById('child-total');
    const seniorTotalDisplay = document.getElementById('senior-total');
    const childRateRow = document.getElementById('child-rate-row');
    const seniorRateRow = document.getElementById('senior-rate-row');

    const formatMoney = (value) => {
        return '₱' + value.toLocaleString('en-PH', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
    };

    const calculateTotals = () => {
        const totalTravelers = parseInt(totalTravelersInput.value) || 0;
        const children = parseInt(childInput.value) || 0;
        const seniors = parseInt(seniorInput.value) || 0;
        const adults = Math.max(0, totalTravelers - children - seniors);
        const guests = totalTravelers;

        hiddenAdultsInput.value = adults;
        guestTotalDisplay.textContent = guests;

        let serviceTotal = 0;
        let guideFee = 0;

        serviceCheckboxes.forEach((checkbox) => {
            if (checkbox.checked) {
                serviceTotal += parseFloat(checkbox.dataset.price) || 0;
            }
        });

        if (guideCheckbox && guideCheckbox.checked) {
            guideFee = parseFloat(guideCheckbox.dataset.price) || 0;
        }

        const childRate = basePrice * 0.5;
        const seniorRate = basePrice * 0.8;
        const adultAmount = adults * basePrice;
        const childAmount = children * childRate;
        const seniorAmount = seniors * seniorRate;
        const baseAmount = adultAmount + childAmount + seniorAmount;
        const discountAmount = promoDiscount > 0 ? baseAmount * (promoDiscount / 100) : 0;
        const total = baseAmount - discountAmount + serviceTotal + guideFee;

        childTotalDisplay.textContent = formatMoney(childAmount);
        seniorTotalDisplay.textContent = formatMoney(seniorAmount);
        childRateRow.style.display = children > 0 ? 'flex' : 'none';
        seniorRateRow.style.display = seniors > 0 ? 'flex' : 'none';
        extrasTotalDisplay.textContent = formatMoney(serviceTotal);
        guideFeeDisplay.textContent = formatMoney(guideFee);
        guideFeeRow.style.display = guideFee > 0 ? 'flex' : 'none';
        totalDisplay.textContent = formatMoney(total);
        if (baseRateDisplay) {
            baseRateDisplay.textContent = formatMoney(basePrice);
        }
        if (discountTotalDisplay) {
            discountTotalDisplay.textContent = formatMoney(discountAmount);
        }

        guestInputs.forEach((input) => {
            if (parseInt(input.value) < 0) {
                input.value = 0;
            }
            if (parseInt(input.value) > maxGuests) {
                input.value = maxGuests;
            }
        });
    };

    const updateCheckOutConstraints = () => {
        if (!checkInInput || !checkOutInput) {
            return;
        }

        const checkInDate = checkInInput.value ? new Date(checkInInput.value) : null;
        if (!checkInDate) {
            return;
        }

        const expectedCheckOut = new Date(checkInDate);
        expectedCheckOut.setDate(expectedCheckOut.getDate() + bookingDurationDays);
        const formatted = expectedCheckOut.toISOString().slice(0, 10);

        checkOutInput.min = formatted;
        checkOutInput.max = formatted;
        if (checkOutInput.value !== formatted) {
            checkOutInput.value = formatted;
        }
    };

    guestInputs.forEach((input) => input.addEventListener('input', calculateTotals));
    serviceCheckboxes.forEach((checkbox) => checkbox.addEventListener('change', calculateTotals));
    if (guideCheckbox) {
        guideCheckbox.addEventListener('change', calculateTotals);
    }
    if (checkInInput) {
        checkInInput.addEventListener('change', updateCheckOutConstraints);
    }
    updateCheckOutConstraints();
    calculateTotals();
</script>

</x-layout>
