<?php

namespace App\Traits;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

trait HandleFileUpload
{
    /**
     * Upload a file to storage
     * 
     * @param UploadedFile $file
     * @param string $directory - storage directory (e.g., 'photos/users', 'photos/coaches')
     * @param string|null $oldPath - old file path to delete if exists
     * @return string - stored file path
     */
    protected function uploadFile(UploadedFile $file, string $directory, ?string $oldPath = null): string
    {
        // Delete old file if exists
        if ($oldPath && Storage::disk('public')->exists($oldPath)) {
            Storage::disk('public')->delete($oldPath);
        }

        // Generate unique filename
        $filename = Str::random(20) . '.' . $file->getClientOriginalExtension();
        
        // Store file
        $path = $file->storeAs($directory, $filename, 'public');

        return $path;
    }

    /**
     * Delete a file from storage
     * 
     * @param string|null $filePath
     * @return bool
     */
    protected function deleteFile(?string $filePath): bool
    {
        if ($filePath && Storage::disk('public')->exists($filePath)) {
            return Storage::disk('public')->delete($filePath);
        }

        return false;
    }

    /**
     * Get full URL for a stored file
     * 
     * @param string|null $filePath
     * @return string|null
     */
    protected function getFileUrl(?string $filePath): ?string
    {
        if ($filePath) {
            return asset('storage/' . $filePath);
        }

        return null;
    }
}
