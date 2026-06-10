<?php

use App\Models\Booking;
use App\Models\TourPackage;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('the application returns a successful response', function () {
    $response = $this->get('/');

    $response->assertStatus(200);
});

test('a tour can be booked in demo mode', function () {
    $user = User::create([
        'name' => 'Test Tourist',
        'email' => 'tourist.test@example.com',
        'password' => bcrypt('password'),
        'role' => 'tourist',
    ]);

    $package = TourPackage::create([
        'name' => 'Test Island Tour',
        'description' => 'A test tour package.',
        'location' => 'Test Location',
        'price' => 1500,
        'duration_days' => 2,
        'max_guests' => 4,
        'status' => 'active',
        'rating' => 4.5,
    ]);

    expect($package->duration_days)->toBe(2);

    $response = $this->actingAs($user)->post(route('bookings.store'), [
        'tour_package_id' => $package->id,
        'tour_start_date' => '2026-06-09',
        'tour_end_date' => '2026-06-11',
        'num_adults' => 2,
        'num_children' => 0,
        'num_seniors' => 0,
        'guest_name' => 'Test Tourist',
        'guest_email' => 'tourist.test@example.com',
        'guest_phone' => '09171234567',
        'special_requests' => 'Vegetarian meals',
    ]);

    $response->assertRedirect();
    $response->assertSessionHasNoErrors();
    expect(Booking::count())->toBe(1);
    expect(Booking::first()->user_id)->toBe($user->id);
});

test('package search filters by category and duration', function () {
    TourPackage::create([
        'name' => 'Bolinao Beach Day Tour',
        'description' => 'Beach and island highlights.',
        'location' => 'Bolinao, Pangasinan',
        'price' => 1200,
        'duration_days' => 1,
        'max_guests' => 6,
        'category' => 'natural',
        'status' => 'active',
        'rating' => 4.8,
    ]);

    TourPackage::create([
        'name' => 'Bolinao Heritage Weekend',
        'description' => 'Church and lighthouse visits.',
        'location' => 'Bolinao, Pangasinan',
        'price' => 3200,
        'duration_days' => 3,
        'max_guests' => 8,
        'category' => 'cultural',
        'status' => 'active',
        'rating' => 4.6,
    ]);

    $this->get(route('packages.index', ['category' => 'natural']))
        ->assertOk()
        ->assertSeeText('Bolinao Beach Day Tour')
        ->assertDontSeeText('Bolinao Heritage Weekend');

    $this->get(route('packages.index', ['duration' => '2_4']))
        ->assertOk()
        ->assertSeeText('Bolinao Heritage Weekend')
        ->assertDontSeeText('Bolinao Beach Day Tour');

    $this->get(route('packages.index', ['duration' => 'all']))
        ->assertOk()
        ->assertSeeText('Bolinao Beach Day Tour')
        ->assertSeeText('Bolinao Heritage Weekend');

    $this->get(route('packages.index', ['capacity' => 7]))
        ->assertOk()
        ->assertSeeText('Bolinao Heritage Weekend')
        ->assertDontSeeText('Bolinao Beach Day Tour');

    $this->get(route('packages.index', ['category' => 'cultural', 'duration' => '2_4']))
        ->assertOk()
        ->assertSeeText('Bolinao Heritage Weekend')
        ->assertDontSeeText('Bolinao Beach Day Tour');
});

test('package details page renders the tour information', function () {
    $user = User::create([
        'name' => 'Details Tourist',
        'email' => 'details.tourist@example.com',
        'password' => bcrypt('password'),
        'role' => 'tourist',
    ]);

    $package = TourPackage::create([
        'name' => 'Bolinao Lighthouse Tour',
        'description' => 'A scenic heritage tour with coastal views.',
        'location' => 'Bolinao, Pangasinan',
        'price' => 1800,
        'duration_days' => 1,
        'max_guests' => 5,
        'category' => 'cultural',
        'status' => 'active',
        'rating' => 4.7,
    ]);

    $this->actingAs($user)->get(route('packages.show', $package))
        ->assertOk()
        ->assertSeeText('Bolinao Lighthouse Tour')
        ->assertSeeText('Reserve This Tour', false)
        ->assertSeeText('Trip overview');
});
