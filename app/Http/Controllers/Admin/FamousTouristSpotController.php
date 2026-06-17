<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\FamousTouristSpot;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class FamousTouristSpotController extends Controller
{
    public function index(): View
    {
        $spots = FamousTouristSpot::orderBy('sort_order')->latest()->paginate(6);
        return view('admin.famous-tourist-spots.index', compact('spots'));
    }

    public function create(): View
    {
        return view('admin.famous-tourist-spots.create');
    }

    public function store(Request $request): RedirectResponse
    {
        \Log::info('Famous tourist spot store request', [
            'has_image' => $request->hasFile('image'),
            'image_exists' => $request->file('image') ? 'yes' : 'no',
            'all_request' => $request->all(),
            'files' => $request->files->all(),
        ]);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'location' => 'required|string|max:255',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
            'is_active' => 'boolean',
            'sort_order' => 'integer',
        ]);

        $validated['is_active'] = $request->has('is_active');
        $validated['sort_order'] = $validated['sort_order'] ?? 0;

        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('famous-tourist-spots', 'public');
            $validated['image'] = $imagePath;
        }

        FamousTouristSpot::create($validated);

        return redirect()->route('admin.famous-tourist-spots.index')
            ->with('success', 'Famous tourist spot created successfully.');
    }

    public function edit(FamousTouristSpot $famousTouristSpot): View
    {
        return view('admin.famous-tourist-spots.edit', compact('famousTouristSpot'));
    }

    public function update(Request $request, FamousTouristSpot $famousTouristSpot): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'location' => 'required|string|max:255',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
            'is_active' => 'boolean',
            'sort_order' => 'integer',
        ]);

        $validated['is_active'] = $request->has('is_active');
        $validated['sort_order'] = $validated['sort_order'] ?? $famousTouristSpot->sort_order;

        if ($request->hasFile('image')) {
            // Delete old image
            if ($famousTouristSpot->image) {
                Storage::disk('public')->delete($famousTouristSpot->image);
            }
            $imagePath = $request->file('image')->store('famous-tourist-spots', 'public');
            $validated['image'] = $imagePath;
        }

        $famousTouristSpot->update($validated);

        return redirect()->route('admin.famous-tourist-spots.index')
            ->with('success', 'Famous tourist spot updated successfully.');
    }

    public function uploadImage(Request $request, FamousTouristSpot $famousTouristSpot): JsonResponse
    {
        try {
            if (!$request->hasFile('image') || !$request->file('image')->isValid()) {
                return response()->json(['error' => 'No valid image file provided'], 422);
            }

            $file = $request->file('image');

            // Delete old image if exists
            if ($famousTouristSpot->image) {
                Storage::disk('public')->delete($famousTouristSpot->image);
            }

            // Store new image
            $path = $file->store('famous-tourist-spots', 'public');
            $famousTouristSpot->image = $path;
            $famousTouristSpot->save();

            // Generate URL
            $url = $famousTouristSpot->image_url;
            $timestamp = time();
            try {
                $timestamp = Storage::disk('public')->lastModified($path);
            } catch (\Throwable $_) {
                $timestamp = time();
            }

            \Log::info('Tourist spot image uploaded', ['path' => $path, 'url' => $url]);

            return response()->json([
                'url' => $url,
                'path' => $path,
                'timestamp' => $timestamp,
            ], 200);
        } catch (\Throwable $exception) {
            \Log::error('Tourist spot image upload failed', [
                'error' => $exception->getMessage(),
                'trace' => $exception->getTraceAsString(),
            ]);
            return response()->json([
                'error' => 'Image upload failed.',
                'message' => $exception->getMessage(),
            ], 500);
        }
    }

    public function destroy(FamousTouristSpot $famousTouristSpot): RedirectResponse
    {
        // Delete image
        if ($famousTouristSpot->image) {
            Storage::disk('public')->delete($famousTouristSpot->image);
        }

        $famousTouristSpot->delete();

        return redirect()->route('admin.famous-tourist-spots.index')
            ->with('success', 'Famous tourist spot deleted successfully.');
    }
}
