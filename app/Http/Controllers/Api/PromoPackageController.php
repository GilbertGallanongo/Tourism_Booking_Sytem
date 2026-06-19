<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\Concerns\AuthorizesApiAccess;
use App\Http\Controllers\Controller;
use App\Models\PromoPackage;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PromoPackageController extends Controller
{
    use AuthorizesApiAccess;

    public function index(): JsonResponse
    {
        return response()->json([
            'data' => PromoPackage::latest()
                ->get()
                ->map(fn (PromoPackage $promoPackage) => $this->promoPackagePayload($promoPackage)),
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $this->requireAdmin($request);

        $validated = $this->validatePromoPackage($request);
        $validated = $this->prepareImageData($request, $validated);

        $validated['is_active'] = $request->boolean('is_active', $validated['is_active'] ?? true);

        if ($request->hasFile('image_file')) {
            $validated['image'] = $this->storeImage($request);
        }

        $promoPackage = PromoPackage::create($validated);

        return response()->json(['data' => $this->promoPackagePayload($promoPackage)], 201);
    }

    public function show(PromoPackage $promoPackage): JsonResponse
    {
        return response()->json(['data' => $this->promoPackagePayload($promoPackage)]);
    }

    public function update(Request $request, PromoPackage $promoPackage): JsonResponse
    {
        $this->requireAdmin($request);

        $validated = $this->validatePromoPackage($request, true);
        $validated = $this->prepareImageData($request, $validated);

        if ($request->has('is_active')) {
            $validated['is_active'] = $request->boolean('is_active');
        }

        if ($request->hasFile('image_file')) {
            $this->deletePublicFile($promoPackage->image);
            $validated['image'] = $this->storeImage($request);
        }

        $promoPackage->update($validated);

        return response()->json(['data' => $this->promoPackagePayload($promoPackage->refresh())]);
    }

    public function uploadImage(Request $request, PromoPackage $promoPackage): JsonResponse
    {
        $this->requireAdmin($request);

        $request->validate([
            'image_file' => ['required', 'image', 'mimes:jpeg,png,jpg,gif,webp', 'max:5120'],
        ]);

        $this->deletePublicFile($promoPackage->image);

        $promoPackage->update([
            'image' => $this->storeImage($request),
        ]);

        return response()->json(['data' => $this->promoPackagePayload($promoPackage->refresh())]);
    }

    public function destroy(Request $request, PromoPackage $promoPackage): JsonResponse
    {
        $this->requireAdmin($request);

        $this->deletePublicFile($promoPackage->image);
        $promoPackage->delete();

        return response()->json(['message' => 'Promo package deleted successfully.']);
    }

    private function validatePromoPackage(Request $request, bool $isUpdate = false): array
    {
        $required = $isUpdate ? 'sometimes' : 'required';

        return $request->validate([
            'name' => [$required, 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'discount_percentage' => [$required, 'numeric', 'min:0', 'max:100'],
            'start_date' => [$required, 'date'],
            'end_date' => [$required, 'date', 'after_or_equal:start_date'],
            'is_active' => ['sometimes', 'boolean'],
            'image' => ['nullable', 'string', 'max:255'],
            'image_file' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif,webp', 'max:5120'],
        ]);
    }

    private function promoPackagePayload(PromoPackage $promoPackage): array
    {
        $data = $promoPackage->toArray();
        $data['image_url'] = $promoPackage->image_url;
        $data['has_image'] = (bool) $promoPackage->image;

        return $data;
    }

    private function storeImage(Request $request): string
    {
        return $request->file('image_file')->store('promo-packages', 'public');
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
