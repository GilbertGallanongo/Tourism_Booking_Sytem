<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Destination;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DestinationController extends Controller
{
    public function index(): View
    {
        $destinations = Destination::latest()->paginate(10);

        return view('admin.destinations.index', compact('destinations'));
    }

    public function create(): View
    {
        return view('admin.destinations.create', ['destination' => new Destination()]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'location' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
        ]);

        Destination::create($data);

        return redirect()->route('admin.destinations.index')
            ->with('success', 'Destination added successfully.');
    }

    public function edit(Destination $destination): View
    {
        return view('admin.destinations.edit', compact('destination'));
    }

    public function update(Request $request, Destination $destination): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'location' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
        ]);

        $destination->update($data);

        return redirect()->route('admin.destinations.index')
            ->with('success', 'Destination updated successfully.');
    }

    public function destroy(Destination $destination): RedirectResponse
    {
        $destination->delete();

        return redirect()->route('admin.destinations.index')
            ->with('success', 'Destination deleted successfully.');
    }
}
