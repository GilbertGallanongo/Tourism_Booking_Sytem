<?php

namespace App\Http\Controllers\Api\Concerns;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

trait HandlesApiImageUploads
{
    protected function imageUploadRules(bool $required = false): array
    {
        return [
            $required ? 'required' : 'nullable',
            'image',
            'mimes:jpeg,png,jpg,gif,webp',
            'max:51200',
        ];
    }

    protected function imageUploadFailureResponse(Request $request, string $field = 'image_file', bool $required = false): ?JsonResponse
    {
        $contentLength = (int) $request->server('CONTENT_LENGTH', 0);
        $postMaxBytes = $this->parseUploadSize(ini_get('post_max_size'));

        if ($contentLength > 0 && $postMaxBytes > 0 && $contentLength > $postMaxBytes) {
            return response()->json([
                'message' => 'The uploaded image is too large for the server request limit.',
                'errors' => [
                    $field => [
                        'The uploaded image is larger than the server post limit of ' . ini_get('post_max_size') . '.',
                    ],
                ],
            ], 422);
        }

        $uploadError = $_FILES[$field]['error'] ?? null;

        if ($uploadError === UPLOAD_ERR_NO_FILE) {
            return $required
                ? $this->imageUploadErrorResponse($field, 'No image file was uploaded.', $uploadError)
                : null;
        }

        if ($uploadError !== null && $uploadError !== UPLOAD_ERR_OK) {
            return $this->imageUploadErrorResponse($field, $this->uploadErrorMessage((int) $uploadError), (int) $uploadError);
        }

        if ($request->hasFile($field) && ! $request->file($field)->isValid()) {
            return $this->imageUploadErrorResponse($field, $this->uploadErrorMessage($request->file($field)->getError()), $request->file($field)->getError());
        }

        if ($required && ! $request->hasFile($field)) {
            return $this->imageUploadErrorResponse($field, 'No image file was uploaded.');
        }

        return null;
    }

    protected function uploadErrorMessage(int $errorCode): string
    {
        return match ($errorCode) {
            UPLOAD_ERR_INI_SIZE => 'The uploaded image exceeds the server upload limit of ' . ini_get('upload_max_filesize') . '.',
            UPLOAD_ERR_FORM_SIZE => 'The uploaded image exceeds the form upload limit.',
            UPLOAD_ERR_PARTIAL => 'The uploaded image was only partially uploaded. Please choose the file again and retry.',
            UPLOAD_ERR_NO_FILE => 'No image file was uploaded.',
            UPLOAD_ERR_NO_TMP_DIR => 'The server is missing a temporary upload folder.',
            UPLOAD_ERR_CANT_WRITE => 'The server could not write the uploaded image to disk.',
            UPLOAD_ERR_EXTENSION => 'A PHP extension stopped the image upload.',
            default => 'The uploaded image failed to upload.',
        };
    }

    private function imageUploadErrorResponse(string $field, string $message, ?int $code = null): JsonResponse
    {
        return response()->json([
            'message' => $message,
            'errors' => [
                $field => [$message],
            ],
            'upload_error_code' => $code,
        ], 422);
    }

    private function parseUploadSize(string|false $size): int
    {
        if ($size === false) {
            return 0;
        }

        $size = trim($size);
        $unit = strtolower(substr($size, -1));
        $value = (float) $size;

        return (int) match ($unit) {
            'g' => $value * 1024 * 1024 * 1024,
            'm' => $value * 1024 * 1024,
            'k' => $value * 1024,
            default => $value,
        };
    }
}
