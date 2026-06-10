<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PromoPackage;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class PromoPackageController extends Controller
{
    public function index(): View
    {
        $promoPackages = PromoPackage::latest()->paginate(10);
        return view('admin.promo-packages.index', compact('promoPackages'));
    }

    public function create(): View
    {
        return view('admin.promo-packages.create', ['promoPackage' => new PromoPackage()]);
    }

    public function store(Request $request): RedirectResponse
    {
        try {
            $data = $request->validate([
                'name' => ['required', 'string', 'max:255'],
                'description' => ['nullable', 'string'],
                'discount_percentage' => ['required', 'numeric', 'min:0', 'max:100'],
                'start_date' => ['required', 'date'],
                'end_date' => ['required', 'date', 'after:start_date'],
                'is_active' => ['boolean'],
                'image' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif,webp', 'max:2048'],
            ]);

            $data['is_active'] = $request->has('is_active');

            if ($request->hasFile('image')) {
                $data['image'] = $request->file('image')->store('promo-packages', 'public');
            }

            PromoPackage::create($data);

            return redirect()->route('admin.promo-packages.index')
                ->with('success', 'Promo package created successfully.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Error creating promo package: ' . $e->getMessage());
        }
    }

    public function edit(PromoPackage $promoPackage): View
    {
        return view('admin.promo-packages.edit', compact('promoPackage'));
    }

    public function update(Request $request, PromoPackage $promoPackage): RedirectResponse
    {
        try {
            \Log::info('Promo package update request', [
                'has_file' => $request->hasFile('image'),
                'file_exists' => $request->file('image') ? 'yes' : 'no',
                'file_size' => $request->file('image') ? $request->file('image')->getSize() : 'N/A',
                'file_valid' => $request->file('image') ? $request->file('image')->isValid() : 'N/A',
                'file_error' => $request->file('image') ? $request->file('image')->getError() : 'N/A',
                'all_data' => $request->all(),
                'files' => $request->files->all()
            ]);

            $data = $request->validate([
                'name' => ['required', 'string', 'max:255'],
                'description' => ['nullable', 'string'],
                'discount_percentage' => ['required', 'numeric', 'min:0', 'max:100'],
                'start_date' => ['required', 'date'],
                'end_date' => ['required', 'date', 'after:start_date'],
                'is_active' => ['boolean'],
                'image' => ['nullable', 'file', 'mimes:jpeg,png,jpg,gif,webp', 'max:2048'],
            ]);

            $data['is_active'] = $request->has('is_active');

            if ($request->hasFile('image') && $request->file('image')->isValid()) {
                if ($promoPackage->image) {
                    Storage::disk('public')->delete($promoPackage->image);
                }
                $data['image'] = $request->file('image')->store('promo-packages', 'public');
            }

            $promoPackage->update($data);

            return redirect()->route('admin.promo-packages.index')
                ->with('success', 'Promo package updated successfully.');
        } catch (\Exception $e) {
            \Log::error('Promo package update error: ' . $e->getMessage(), [
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString(),
                'request_data' => $request->all(),
                'files' => $request->files->all(),
                'validation_errors' => $e instanceof \Illuminate\Validation\ValidationException ? $e->errors() : []
            ]);
            return redirect()->back()
                ->withInput()
                ->with('error', 'Error updating promo package: ' . $e->getMessage());
        }
    }

    public function destroy(PromoPackage $promoPackage): RedirectResponse
    {
        if ($promoPackage->image) {
            Storage::disk('public')->delete($promoPackage->image);
        }

        $promoPackage->delete();

        return redirect()->route('admin.promo-packages.index')
            ->with('success', 'Promo package deleted successfully.');
    }
}
