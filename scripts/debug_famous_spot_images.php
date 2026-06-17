<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\FamousTouristSpot;

foreach (FamousTouristSpot::orderBy('sort_order')->get() as $spot) {
    echo $spot->id . ' | ' . $spot->name . ' | ' . ($spot->image ?? '[null]') . ' | ' . $spot->image_url . PHP_EOL;
}
