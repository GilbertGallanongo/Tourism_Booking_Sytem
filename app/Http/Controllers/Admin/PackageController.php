<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Destination;
use App\Models\TourPackage;
use App\Support\UploadedImage;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class PackageController extends Controller
{
    public function index(Request $request): View
    {
        $search = trim((string) $request->query('search', $request->query('q', '')));

        $packages = TourPackage::latest()
            ->search($search)
            ->when($request->category, function ($query) use ($request) {
                $query->where('category', $request->category);
            })
            ->paginate(6)
            ->withQueryString();

        $categories = TourPackage::categoryLabels();

        return view('admin.packages.index', compact('packages', 'categories', 'search'));
    }

    public function create(): View
    {
        return view('admin.packages.create', [
            'package' => new TourPackage(),
            'destinations' => Destination::orderBy('name')->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        try {
            $data = $this->preparePackageData($request);
            TourPackage::create($data);

            return redirect()
                ->route('admin.packages.index')
                ->with('success', 'Tour package added successfully.');
        } catch (\Illuminate\Validation\ValidationException $e) {
            \Log::warning('Package creation validation failed', [
                'errors' => $e->errors(),
            ]);
            return redirect()
                ->route('admin.packages.create')
                ->withErrors($e->errors())
                ->withInput($request->all());
        } catch (\Exception $e) {
            \Log::error('Package creation failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            return redirect()
                ->route('admin.packages.create')
                ->with('error', 'An error occurred while creating the package: ' . $e->getMessage())
                ->withInput($request->all());
        }
    }

    public function show(TourPackage $package): View
    {
        return view('admin.packages.show', compact('package'));
    }

    public function edit(TourPackage $package): View
    {
        return view('admin.packages.edit', [
            'package' => $package,
            'destinations' => Destination::orderBy('name')->get(),
        ]);
    }

    public function update(Request $request, TourPackage $package): RedirectResponse
    {
        try {
            $data = $this->preparePackageData($request, $package);
            $package->update($data);

            return redirect()
                ->route('admin.packages.index')
                ->with('success', 'Tour package updated successfully.');
        } catch (\Illuminate\Validation\ValidationException $e) {
            \Log::warning('Package update validation failed', [
                'package_id' => $package->id,
                'errors' => $e->errors(),
            ]);
            return redirect()
                ->route('admin.packages.edit', $package)
                ->withErrors($e->errors())
                ->withInput($request->all());
        } catch (\Exception $e) {
            \Log::error('Package update failed', [
                'package_id' => $package->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            return redirect()
                ->route('admin.packages.edit', $package)
                ->with('error', 'An error occurred while updating the package: ' . $e->getMessage())
                ->withInput($request->all());
        }
    }

    public function uploadImage(Request $request, TourPackage $package): JsonResponse
    {
        try {
            // Try to increase PHP limits - may not work depending on server configuration
            @ini_set('upload_max_filesize', '500M');
            @ini_set('post_max_size', '500M');
            @ini_set('memory_limit', '512M');
            @ini_set('max_execution_time', '300');

            $uploadLimit = ini_get('upload_max_filesize');
            $postLimit = ini_get('post_max_size');
            $tmpDir = ini_get('upload_tmp_dir') ?: sys_get_temp_dir();

            \Log::info('Upload image request received', [
                'package_id' => $package->id,
                'has_file' => $request->hasFile('image_file'),
                'files' => array_keys($request->allFiles()),
                'uploaded' => $_FILES['image_file'] ?? null,
                'upload_max_filesize' => $uploadLimit,
                'post_max_size' => $postLimit,
                'upload_tmp_dir' => $tmpDir,
            ]);

            $uploadError = $_FILES['image_file']['error'] ?? null;
            if ($uploadError !== null && $uploadError !== UPLOAD_ERR_OK) {
                $message = $this->uploadErrorMessage($uploadError);
                \Log::warning('Uploaded file has PHP upload error', [
                    'error_code' => $uploadError,
                    'message' => $message,
                    'upload_max_filesize' => $uploadLimit,
                    'post_max_size' => $postLimit,
                    'files' => $_FILES['image_file'] ?? null,
                ]);
                return response()->json(['error' => $message, 'code' => $uploadError], 422);
            }

            // Check if file was actually sent
            if (! isset($_FILES['image_file']) || $_FILES['image_file']['error'] === UPLOAD_ERR_NO_FILE) {
                \Log::warning('No file was uploaded', ['files' => $_FILES]);
                return response()->json(['error' => 'No file was uploaded.'], 422);
            }

            // Validate using Laravel's built-in validation only after PHP upload succeeds
            if (! $request->hasFile('image_file')) {
                \Log::warning('Request has no image_file', [
                    'all_files' => array_keys($request->allFiles()),
                    'php_files' => array_keys($_FILES),
                ]);
                return response()->json(['error' => 'Uploaded image is invalid or missing.'], 422);
            }

            $file = $request->file('image_file');
            if (! $file->isValid()) {
                \Log::warning('Uploaded file is not valid', [
                    'error' => $file->getError(),
                ]);
                return response()->json(['error' => 'The uploaded file is not valid.'], 422);
            }

            // store using the public disk so paths are consistent and served via /storage
            $file = $request->file('image_file');
            $name = time() . '-' . uniqid() . '-' . preg_replace('/[^a-z0-9\-\.]/i', '-', $file->getClientOriginalName());

            // delete old image from public disk if present
            if ($package->image && ! str_starts_with($package->image, 'http')) {
                $old = ltrim($package->image, '/');
                if (Storage::disk('public')->exists($old)) {
                    \Log::info('Deleting old image from storage disk', ['old' => $old]);
                    Storage::disk('public')->delete($old);
                } elseif (File::exists(public_path($old))) {
                    \Log::info('Deleting old image from public path', ['old_path' => public_path($old)]);
                    File::delete(public_path($old));
                }
            }

            try {
                $path = $this->storePackageImage($request, $package);
                if ($path === null) {
                    return response()->json(['error' => 'Could not save uploaded image.'], 500);
                }
                $package->image = $path;
                $package->save();
                \Log::info('File stored on public disk', ['path' => $path]);
            } catch (\Throwable $exception) {
                \Log::error('File store failed', [
                    'error' => $exception->getMessage(),
                    'trace' => $exception->getTraceAsString(),
                ]);
                return response()->json(['error' => 'Could not save uploaded image.', 'details' => $exception->getMessage()], 500);
            }
            
            \Log::info('Package image updated in DB', ['image' => $package->image]);

            $url = UploadedImage::url($package->image);
            $timestamp = time();
            if (Storage::disk('public')->exists($package->image)) {
                try {
                    $timestamp = Storage::disk('public')->lastModified($package->image);
                } catch (\Throwable $_) {
                    $timestamp = time();
                }
            }

            return response()->json([
                'url' => $url,
                'path' => $package->image,
                'timestamp' => $timestamp,
            ], 200);
        } catch (\Illuminate\Validation\ValidationException $exception) {
            \Log::warning('Validation failed', ['errors' => $exception->errors()]);
            return response()->json(['error' => 'Validation failed', 'errors' => $exception->errors()], 422);
        } catch (\Throwable $exception) {
            \Log::error('Unexpected error in uploadImage', [
                'error' => $exception->getMessage(),
                'trace' => $exception->getTraceAsString(),
            ]);
            return response()->json([
                'error' => 'Image upload failed.',
                'message' => $exception->getMessage(),
            ], 500);
        }
    }

    /**
     * Receive a single chunk and store it temporarily.
     */
    public function uploadChunk(Request $request, TourPackage $package): JsonResponse
    {
        $validated = $request->validate([
            'upload_id' => ['required', 'string'],
            'chunk_index' => ['required', 'integer'],
            'total_chunks' => ['required', 'integer'],
            'chunk' => ['required', 'file'],
        ]);

        $uploadId = $validated['upload_id'];
        $index = $validated['chunk_index'];

        $tmpDir = 'uploads/tmp/' . $uploadId;
        $disk = Storage::disk('local');
        try {
            // ensure base uploads/tmp exists
            if (! $disk->exists('uploads/tmp')) {
                $disk->makeDirectory('uploads/tmp');
            }
            // ensure tmp dir exists for this upload id
            if (! $disk->exists($tmpDir)) {
                $disk->makeDirectory($tmpDir);
            }

            $stored = $disk->putFileAs($tmpDir, $request->file('chunk'), 'chunk_' . $index);
            $absPath = $disk->path($tmpDir) . DIRECTORY_SEPARATOR . 'chunk_' . $index;

            // verify file exists after write
            $exists = file_exists($absPath) || $disk->exists($tmpDir . '/chunk_' . $index);

            \Log::info('Chunk stored', [
                'upload_id' => $uploadId,
                'chunk_index' => $index,
                'tmp_dir' => $disk->path($tmpDir),
                'stored' => $stored,
                'exists' => $exists,
            ]);

            if (! $exists) {
                throw new \RuntimeException('Chunk was not persisted to disk');
            }
        } catch (\Throwable $e) {
            \Log::error('Chunk store failed', ['error' => $e->getMessage(), 'upload_id' => $uploadId, 'chunk_index' => $index]);
            return response()->json(['error' => 'Could not store chunk', 'message' => $e->getMessage()], 500);
        }

        return response()->json(['ok' => true, 'index' => $index]);
    }

    /**
     * Assemble uploaded chunks into final file and attach to package.
     */
    public function completeUpload(Request $request, TourPackage $package): JsonResponse
    {
        $validated = $request->validate([
            'upload_id' => ['required', 'string'],
            'original_name' => ['required', 'string'],
            'total_chunks' => ['required', 'integer'],
        ]);

        $uploadId = $validated['upload_id'];
        $total = (int) $validated['total_chunks'];
        $original = $validated['original_name'];

        $tmpDir = Storage::disk('local')->path('uploads/tmp/' . $uploadId);
        $diskRelative = 'uploads/tmp/' . $uploadId;
        $disk = Storage::disk('local');
        $exists = $disk->exists($diskRelative);
        if (! is_dir($tmpDir) || ! $exists) {
            // attempt to find a matching uploads tmp dir as a fallback
            $candidates = [];
            try {
                $allDirs = [];
                if ($disk->exists('uploads/tmp')) {
                    $allDirs = $disk->allDirectories('uploads/tmp');
                }
                foreach ($allDirs as $d) {
                    $allFiles = $disk->allFiles($d);
                    $chunkCount = 0;
                    foreach ($allFiles as $f) {
                        if (preg_match('/chunk_\d+$/', basename($f))) {
                            $chunkCount++;
                        }
                    }
                    if ($chunkCount >= $total) {
                        $candidates[] = ['dir' => $d, 'count' => $chunkCount];
                    }
                }
            } catch (\Throwable $_) {
                $candidates = [];
            }

            if (! empty($candidates)) {
                // pick the best candidate (first with sufficient chunks)
                $chosen = $candidates[0]['dir'];
                $tmpDir = $disk->path($chosen);
                \Log::warning('Chunk complete: using fallback tmp dir', [
                    'requested_upload_id' => $uploadId,
                    'chosen_dir' => $chosen,
                    'chosen_abs' => $tmpDir,
                    'candidate_count' => $candidates[0]['count'],
                ]);
            } else {
                // gather diagnostic info
                $files = [];
                try {
                    if ($disk->exists('uploads/tmp')) {
                        $files = $disk->allFiles('uploads/tmp');
                    }
                } catch (\Throwable $_) {
                    $files = [];
                }

                \Log::warning('Chunk complete failed: tmp dir missing or empty', [
                    'upload_id' => $uploadId,
                    'tmp_dir' => $tmpDir,
                    'disk_root' => $disk->path(''),
                    'disk_relative' => $diskRelative,
                    'disk_exists' => $exists,
                    'uploads_tmp_listing' => $files,
                ]);

                return response()->json([
                    'error' => 'Upload not found',
                    'debug' => [
                        'tmp_dir' => $tmpDir,
                        'disk_root' => $disk->path(''),
                        'disk_relative' => $diskRelative,
                        'disk_exists' => $exists,
                        'uploads_tmp_listing' => $files,
                    ],
                ], 404);
            }
        }

        $name = time() . '-' . uniqid() . '-' . preg_replace('/[^a-z0-9\-\.]/i', '-', $original);

        if (! Storage::disk('public')->exists('images')) {
            Storage::disk('public')->makeDirectory('images');
        }

        $publicPath = Storage::disk('public')->path('images/' . $name);
        $imagePath = 'images/' . $name;

        $out = fopen($publicPath, 'wb');
        if ($out === false) {
            return response()->json(['error' => 'Could not create destination file'], 500);
        }

        try {
            for ($i = 0; $i < $total; $i++) {
                $chunkPath = $tmpDir . DIRECTORY_SEPARATOR . 'chunk_' . $i;
                if (! file_exists($chunkPath)) {
                    fclose($out);
                    return response()->json(['error' => "Missing chunk {$i}"], 422);
                }
                $in = fopen($chunkPath, 'rb');
                stream_copy_to_stream($in, $out);
                fclose($in);
            }
            fclose($out);

            // store record and cleanup
            $package->image = $imagePath;
            $package->save();

            // delete tmp chunks and cleanup directory
            Storage::disk('local')->deleteDirectory('uploads/tmp/' . $uploadId);

            $url = UploadedImage::url($package->image);
            return response()->json(['url' => $url, 'path' => $package->image], 200);
        } catch (\Throwable $e) {
            if (is_resource($out)) { fclose($out); }
            \Log::error('Chunk assemble failed', ['error' => $e->getMessage()]);
            return response()->json(['error' => 'Could not assemble file'], 500);
        }
    }

    public function destroy(TourPackage $package): RedirectResponse
    {
        $package->delete();

        return redirect()
            ->route('admin.packages.index')
            ->with('success', 'Tour package removed successfully.');
    }

    private function preparePackageData(Request $request, ?TourPackage $package = null): array
    {
        $data = $this->validatePackage($request, $package);
        // ensure non-nullable DB columns have safe defaults
        if (! array_key_exists('description', $data) || $data['description'] === null) {
            $data['description'] = '';
        }

        // Remove null values so DB defaults (e.g. rating) can apply instead of inserting NULL
        foreach ($data as $key => $value) {
            if ($value === null) {
                unset($data[$key]);
            }
        }

        // Process file uploads for both create and update.
        // This ensures package image uploads still work when JS/AJAX fails.
        if ($request->hasFile('image_file') && $request->file('image_file')->isValid()) {
            $imagePath = $this->storePackageImage($request, $package);
            if ($imagePath !== null) {
                $data['image'] = $imagePath;
            }
        }

        unset($data['image_file'], $data['rating']);

        return $data;
    }

    private function storePackageImage(Request $request, ?TourPackage $package = null): ?string
    {
        if (! $request->hasFile('image_file')) {
            return null;
        }

        $file = $request->file('image_file');
        if (! $file->isValid()) {
            return null;
        }

        $name = time() . '-' . uniqid() . '-' . preg_replace('/[^a-z0-9\-\.]/i', '-', $file->getClientOriginalName());
        $path = 'images/' . $name;

        if ($package && $package->image && ! str_starts_with($package->image, 'http')) {
            $old = $this->normalizeImagePath($package->image);
            if ($old !== '') {
                if (Storage::disk('public')->exists($old)) {
                    Storage::disk('public')->delete($old);
                } elseif (File::exists(public_path($old))) {
                    File::delete(public_path($old));
                }
            }
        }

        // Ensure the images directory exists on the public disk
        if (! Storage::disk('public')->exists('images')) {
            Storage::disk('public')->makeDirectory('images');
        }

        if (Storage::disk('public')->putFileAs('images', $file, $name)) {
            return $path;
        }

        return null;
    }

    private function normalizeImagePath(string $imagePath): string
    {
        $normalized = ltrim($imagePath, '/');
        $normalized = preg_replace('#^(public/|storage/|public/storage/)#i', '', $normalized);

        return ltrim($normalized, '/');
    }

    private function validatePackage(Request $request, ?TourPackage $package = null): array
    {
        $request->merge([
            'time_start' => $this->normalizeTimeInput($request->input('time_start')),
            'time_end' => $this->normalizeTimeInput($request->input('time_end')),
        ]);

        return $request->validate([
            'destination_id' => ['nullable', 'exists:destinations,id'],
            'category' => ['nullable', Rule::in(['natural','cultural','recreational','accommodation','events','ecotourism'])],
            'name' => ['required', 'string', 'max:255'],
            'location' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'price' => ['required', 'numeric', 'min:0'],
            'duration_days' => ['required', 'integer', 'min:1'],
            'max_guests' => ['required', 'integer', 'min:1'],
            'time_start' => ['nullable', 'date_format:H:i'],
            'time_end' => ['nullable', 'date_format:H:i', 'after:time_start'],
            'image' => ['nullable', 'string', 'max:255'],
            // Allow any image size here; large files are handled via chunked uploads.
            'image_file' => ['nullable', 'image'],
            'status' => ['required', 'in:active,inactive'],
        ]);
    }

    private function normalizeTimeInput(mixed $value): ?string
    {
        if ($value === null || $value === '') {
            return null;
        }

        $value = trim((string) $value);

        if (! preg_match('/^(\d{1,2}):(\d{2})(?::\d{2})?$/', $value, $matches)) {
            return $value;
        }

        $hours = (int) $matches[1];
        $minutes = (int) $matches[2];

        if ($hours > 23 || $minutes > 59) {
            return $value;
        }

        return sprintf('%02d:%02d', $hours, $minutes);
    }

    private function uploadErrorMessage(int $errorCode): string
    {
        return match ($errorCode) {
            UPLOAD_ERR_INI_SIZE => 'The uploaded file exceeds the server upload limit.',
            UPLOAD_ERR_FORM_SIZE => 'The uploaded file exceeds the form limit.',
            UPLOAD_ERR_PARTIAL => 'The uploaded file was only partially uploaded.',
            UPLOAD_ERR_NO_FILE => 'No file was uploaded.',
            UPLOAD_ERR_NO_TMP_DIR => 'Missing a temporary folder on the server.',
            UPLOAD_ERR_CANT_WRITE => 'Failed to write file to disk.',
            UPLOAD_ERR_EXTENSION => 'A PHP extension stopped the file upload.',
            default => 'The uploaded file failed to upload.',
        };
    }
}
