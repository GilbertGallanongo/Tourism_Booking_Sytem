<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\TourPackage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class TourPackageController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json([
            'data' => TourPackage::active()->bolinao()->latest()->get(),
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $this->validatePackage($request);

        $package = TourPackage::create($validated);

        return response()->json(['data' => $package], 201);
    }

    public function show(TourPackage $package): JsonResponse
    {
        return response()->json(['data' => $package->load('bookings')]);
    }

    public function update(Request $request, TourPackage $package): JsonResponse
    {
        $validated = $this->validatePackage($request, $package->id);
        $package->update($validated);

        return response()->json(['data' => $package->refresh()]);
    }

    public function destroy(TourPackage $package): JsonResponse
    {
        $package->delete();

        return response()->json(['message' => 'Package deleted successfully.']);
    }

    private function validatePackage(Request $request, ?int $ignoreId = null): array
    {
        return $request->validate([
            'name' => ['sometimes', 'required', 'string', 'max:255'],
            'location' => ['sometimes', 'required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'price' => ['sometimes', 'required', 'numeric', 'min:0'],
            'duration_days' => ['sometimes', 'required', 'integer', 'min:1'],
            'max_guests' => ['sometimes', 'required', 'integer', 'min:1'],
            'image' => ['nullable', 'string', 'max:255'],
            'status' => ['sometimes', 'required', 'in:active,inactive'],
            'rating' => ['nullable', 'numeric', 'between:0,5'],
        ]);
    }

    private function uniqueSlug(string $title, ?int $ignoreId = null): string
    {
        $baseSlug = Str::slug($title);
        $slug = $baseSlug;
        $counter = 2;

        while (TourPackage::where('slug', $slug)
            ->when($ignoreId, fn ($query) => $query->where('id', '!=', $ignoreId))
            ->exists()) {
            $slug = $baseSlug . '-' . $counter;
            $counter++;
        }

        return $slug;
    }
}
