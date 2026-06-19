<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\Concerns\AuthorizesApiAccess;
use App\Http\Controllers\Controller;
use App\Models\FamousTouristSpot;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class FamousTouristSpotController extends Controller
{
    use AuthorizesApiAccess;

    public function index(): JsonResponse
    {
        return response()->json([
            'data' => FamousTouristSpot::where('is_active', true)
                ->orderBy('sort_order')
                ->latest()
                ->get()
                ->map(fn (FamousTouristSpot $spot) => $this->spotPayload($spot)),
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $this->requireAdmin($request);

        $validated = $this->validateSpot($request);
        $validated = $this->prepareImageData($request, $validated);

        $validated['is_active'] = $request->boolean('is_active', $validated['is_active'] ?? true);
        $validated['sort_order'] = $validated['sort_order'] ?? 0;

        if ($request->hasFile('image_file')) {
            $validated['image'] = $this->storeImage($request);
        }

        $spot = FamousTouristSpot::create($validated);

        return response()->json(['data' => $this->spotPayload($spot)], 201);
    }

    public function show(FamousTouristSpot $famousTouristSpot): JsonResponse
    {
        return response()->json(['data' => $this->spotPayload($famousTouristSpot)]);
    }

    public function update(Request $request, FamousTouristSpot $famousTouristSpot): JsonResponse
    {
        $this->requireAdmin($request);

        $validated = $this->validateSpot($request, true);
        $validated = $this->prepareImageData($request, $validated);

        if ($request->has('is_active')) {
            $validated['is_active'] = $request->boolean('is_active');
        }

        if ($request->hasFile('image_file')) {
            $this->deletePublicFile($famousTouristSpot->image);
            $validated['image'] = $this->storeImage($request);
        }

        $famousTouristSpot->update($validated);

        return response()->json(['data' => $this->spotPayload($famousTouristSpot->refresh())]);
    }

    public function uploadImage(Request $request, FamousTouristSpot $famousTouristSpot): JsonResponse
    {
        $this->requireAdmin($request);

        $request->validate([
            'image_file' => ['required', 'image', 'mimes:jpeg,png,jpg,gif,webp', 'max:5120'],
        ]);

        $this->deletePublicFile($famousTouristSpot->image);

        $famousTouristSpot->update([
            'image' => $this->storeImage($request),
        ]);

        return response()->json(['data' => $this->spotPayload($famousTouristSpot->refresh())]);
    }

    public function destroy(Request $request, FamousTouristSpot $famousTouristSpot): JsonResponse
    {
        $this->requireAdmin($request);

        $this->deletePublicFile($famousTouristSpot->image);
        $famousTouristSpot->delete();

        return response()->json(['message' => 'Tourist spot deleted successfully.']);
    }

    private function validateSpot(Request $request, bool $isUpdate = false): array
    {
        $required = $isUpdate ? 'sometimes' : 'required';

        return $request->validate([
            'name' => [$required, 'string', 'max:255'],
            'description' => [$required, 'string'],
            'location' => [$required, 'string', 'max:255'],
            'is_active' => ['sometimes', 'boolean'],
            'sort_order' => ['nullable', 'integer'],
            'image' => ['nullable', 'string', 'max:255'],
            'image_file' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif,webp', 'max:5120'],
        ]);
    }

    private function spotPayload(FamousTouristSpot $spot): array
    {
        $data = $spot->toArray();
        $data['image_url'] = $spot->image_url;
        $data['has_image'] = (bool) $spot->image;

        return $data;
    }

    private function storeImage(Request $request): string
    {
        return $request->file('image_file')->store('famous-tourist-spots', 'public');
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
}
