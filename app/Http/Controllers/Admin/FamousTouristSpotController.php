<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\FamousTouristSpot;
use Illuminate\Http\Request;
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
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'location' => 'required|string|max:255',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
            'is_active' => 'boolean',
            'sort_order' => 'integer',
        ]);

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
