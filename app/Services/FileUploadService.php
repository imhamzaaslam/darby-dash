<?php

namespace App\Services;

use App\Helpers\FileData;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class FileUploadService
{
    /**
     * Upload a file.
     *
     * @param UploadedFile $file
     * @param string $directory
     * @param string|null $disk
     * @param string|null $fileName
     * @return FileData
     */
    public function uploadFile(
        UploadedFile $file,
        string $directory,
        string $disk = null,
        string $fileName = null
    ): FileData {
        // If no disk is specified, use the default disk from the filesystem config
        $disk = $disk ?? config('filesystems.default');
        // Generate a unique filename if not provided
        $fileName = $fileName ?? uniqid().".".$file->getClientOriginalExtension();
        if (!Storage::disk($disk)->exists($directory)) {
            // Directory doesn't exist, so create it
            Storage::disk($disk)->makeDirectory($directory);
        }
        $path = $file->storeAs($directory, $fileName, ['disk' => $disk]);

        //add tenant folder name in start if you are using tenant domain.
        $tenantId = tenancy()->tenant ? tenancy()->tenant->id : null;
        if ($tenantId) {
            $path = "{$tenantId}/{$path}";
        }

        $url = Storage::disk($disk)->url($directory . '/' . $fileName);

        // remove two slashes after the protocol
        $url = preg_replace('/([^:])(\/{2,})/', '$1/', $url);

        return new FileData(
            name: $file->getClientOriginalName(),
            path: $path,
            url: $url,
            mimeType: $file->getMimeType(),
            size: $file->getSize()
        );
    }

    /**
     * Delete a file.
     *
     * @param string $filePath
     * @param string|null $disk
     * @return bool
     */
    public function deleteFile(string $filePath, string $disk = null): bool
    {
        $disk = $disk ?? config('filesystems.default');
        return Storage::disk($disk)->delete($filePath);
    }

    /**
     * Get the URL for a stored file.
     *
     * @param string $filePath
     * @param string|null $disk
     * @return string|null
     */
    public function getFileUrl(string $filePath, ?string $disk = null): ?string
    {
        $disk = $disk ?? config('filesystems.default');
        return Storage::disk($disk)->url($filePath);
    }
}
