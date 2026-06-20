<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\Concerns\AuthorizesApiAccess;
use App\Http\Controllers\Controller;
use App\Models\TourPackage;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class TourPackageController extends Controller
{
    use AuthorizesApiAccess;

    public function index(Request $request): JsonResponse
    {
        $search = trim((string) $request->query('search', $request->query('q', '')));

        return response()->json([
            'data' => TourPackage::active()
                ->search($search)
                ->when($request->filled('category'), fn ($query) => $query->where('category', $request->query('category')))
                ->when($request->integer('capacity') > 0, fn ($query) => $query->where('max_guests', '>=', $request->integer('capacity')))
                ->when($request->integer('max_price') > 0, fn ($query) => $query->where('price', '<=', $request->integer('max_price')))
                ->latest()
                ->get()
                ->map(fn (TourPackage $package) => $this->packagePayload($package)),
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $this->requireAdmin($request);

        $validated = $this->validatePackage($request);
        $validated = $this->prepareImageData($request, $validated);

        if ($request->hasFile('image_file')) {
            $validated['image'] = $this->storeImage($request, 'image_file');
        }

        $package = TourPackage::create($validated);

        return response()->json(['data' => $this->packagePayload($package)], 201);
    }

    public function show(TourPackage $package): JsonResponse
    {
        return response()->json(['data' => $this->packagePayload($package->load('bookings'))]);
    }

    public function update(Request $request, TourPackage $package): JsonResponse
    {
        $this->requireAdmin($request);

        $validated = $this->validatePackage($request, $package->id);
        $validated = $this->prepareImageData($request, $validated);

        if ($request->hasFile('image_file')) {
            $this->deletePublicFile($package->image);
            $validated['image'] = $this->storeImage($request, 'image_file');
        }

        $package->update($validated);

        return response()->json(['data' => $this->packagePayload($package->refresh())]);
    }

    public function uploadImage(Request $request, TourPackage $package): JsonResponse
    {
        $this->requireAdmin($request);

        $request->validate([
            'image_file' => ['required', 'image', 'max:10240'],
        ]);

        $this->deletePublicFile($package->image);

        $package->update([
            'image' => $this->storeImage($request, 'image_file'),
        ]);

        return response()->json(['data' => $this->packagePayload($package->refresh())]);
    }

    public function destroy(Request $request, TourPackage $package): JsonResponse
    {
        $this->requireAdmin($request);

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
            'image_file' => ['nullable', 'image', 'max:10240'],
            'status' => ['sometimes', 'required', 'in:active,inactive'],
            'rating' => ['nullable', 'numeric', 'between:0,5'],
        ]);
    }

    private function packagePayload(TourPackage $package): array
    {
        $data = $package->toArray();
        $data['image_url'] = $package->image_url;
        $data['has_image'] = $package->has_image;

        return $data;
    }

    private function storeImage(Request $request, string $field): string
    {
        return $request->file($field)->store('images', 'public');
    }

    private function prepareImageData(Request $request, array $validated): array
    {
        unset($validated['image_file']);

        if (! $request->hasFile('image_file') && array_key_exists('image', $validated)) {
            $image = $validated['image'];

            if ($image === null || trim((string) $image) === '') {
                unset($validated['image']);
            }
        }

        return $validated;
    }

    private function deletePublicFile(?string $path): void
    {
        $path = ltrim((string) $path, '/');
        $path = preg_replace('#^(public/storage/|public/|storage/)#i', '', $path);

        if ($path !== '' && ! str_starts_with($path, 'http') && Storage::disk('public')->exists($path)) {
            Storage::disk('public')->delete($path);
        }
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
