<?php

namespace App\Http\Controllers\Tourist;

use App\Http\Controllers\Controller;
use App\Models\PromoPackage;
use App\Models\TourPackage;
use Illuminate\Http\Request;

class PackageController extends Controller
{
    public function index(Request $request)
    {
        $categoryMap = [
            'natural' => ['label' => 'Natural Attractions', 'keywords' => ['beach','waterfall','falls','cave','river','rock','island','lagoon','spring','shore']],
            'cultural' => ['label' => 'Cultural & Historical Sites', 'keywords' => ['church','heritage','museum','lighthouse','parish','histor','monument','temple']],
            'recreational' => ['label' => 'Recreational & Adventure Spots', 'keywords' => ['island','diving','snorkel','camp','hike','hiking','island hopping','adventure','tour']],
            'accommodation' => ['label' => 'Accommodation & Hospitality', 'keywords' => ['resort','hotel','inn','homestay','transient','guesthouse','lodg','villa']],
            'events' => ['label' => 'Events & Festivals', 'keywords' => ['festival','event','parade','competition','celebration']],
            'ecotourism' => ['label' => 'Ecotourism & Conservation Areas', 'keywords' => ['mangrove','park','reserve','ecolodge','protected','sanctuary']],
        ];
        $selectedDuration = $request->input('duration', 'all');

        if (! in_array($selectedDuration, ['all', '1', '2_4'], true)) {
            $selectedDuration = 'all';
        }

        if ($request->boolean('dur_1') && ! $request->boolean('dur_all')) {
            $selectedDuration = '1';
        } elseif ($request->boolean('dur_2') && ! $request->boolean('dur_all')) {
            $selectedDuration = '2_4';
        }

        $capacity = $request->filled('capacity') && $request->integer('capacity') > 0
            ? $request->integer('capacity')
            : null;

        $packages = TourPackage::active()
            ->bolinao()
            ->whereIn('id', [5, 6, 7, 8, 9, 10])
            ->withAvg('reviews', 'rating')
            ->withCount('reviews')
            ->when($request->search, fn($q) =>
                $q->where(function($sub) use ($request) {
                    $sub->where('name', 'like', "%{$request->search}%")
                        ->orWhere('location', 'like', "%{$request->search}%")
                        ->orWhere('description', 'like', "%{$request->search}%");
                })
            )
            ->when($request->category && array_key_exists($request->category, $categoryMap), function($q) use ($request, $categoryMap) {
                $q->where(function($sub) use ($request, $categoryMap) {
                    $sub->where('category', $request->category);
                    $keywords = $categoryMap[$request->category]['keywords'];
                    $sub->orWhere(function($s2) use ($keywords) {
                        foreach ($keywords as $k) {
                            $s2->orWhere('name', 'like', "%{$k}%")
                               ->orWhere('description', 'like', "%{$k}%")
                               ->orWhere('location', 'like', "%{$k}%");
                        }
                    })->orWhereHas('destination', function($d) use ($keywords) {
                        foreach ($keywords as $k) {
                            $d->orWhere('name', 'like', "%{$k}%");
                        }
                    });
                });
            })
            ->when($selectedDuration !== 'all', function($q) use ($selectedDuration) {
                if ($selectedDuration === '1') {
                    $q->where('duration_days', 1);
                } elseif ($selectedDuration === '2_4') {
                    $q->whereBetween('duration_days', [2, 4]);
                }
            })
            ->when($capacity, fn($q) => $q->where('max_guests', '>=', $capacity))
            ->when($request->type, fn($q) => $q->where('type', $request->type))
            ->when($request->max_price, fn($q) => $q->where('price', '<=', $request->max_price))
            ->orderByDesc('reviews_avg_rating')
            ->paginate(6)
            ->withQueryString();

        return view('tourist.packages.index', compact('packages', 'categoryMap', 'selectedDuration', 'capacity'));
    }

    public function show(TourPackage $tourPackage)
    {
        abort_if($tourPackage->status === 'inactive', 404);

        $tourPackage->load(['reviews.user', 'destination']);

        $selectedPromoId = request('promo');
        $selectedPromo = null;
        if ($selectedPromoId) {
            $promo = PromoPackage::find($selectedPromoId);
            $selectedPromo = $promo?->isActive() ? $promo : null;
        }

        return view('tourist.packages.show', compact('tourPackage', 'selectedPromo'));
    }
}
