<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ImageUploader
{
    /**
     * Upload a file to external storage or fallback to local disk.
     *
     * @param  \Illuminate\Http\UploadedFile  $file
     * @param  string  $fallbackFolder
     * @return string|null The public URL of the uploaded image
     */
    public static function upload($file, $fallbackFolder = 'uploads')
    {
        if (!$file) {
            return null;
        }

        // 1. Try Catbox.moe first (Permanent storage, unblocked on Render)
        try {
            $response = Http::withoutVerifying()->timeout(5)->attach(
                'fileToUpload',
                file_get_contents($file->getRealPath()),
                $file->getClientOriginalName()
            )->post('https://catbox.moe/user/api.php', [
                'reqtype' => 'fileupload'
            ]);

            if ($response->successful()) {
                $url = trim($response->body());
                if (filter_var($url, FILTER_VALIDATE_URL)) {
                    return $url;
                }
            }
        } catch (\Exception $e) {
            Log::warning('Catbox upload failed or timed out, trying Uguu.se: ' . $e->getMessage());
        }

        // 2. Try Uguu.se fallback (48 hours retention, unblocked in Indonesia)
        try {
            $response = Http::withoutVerifying()->timeout(10)->attach(
                'files[]',
                file_get_contents($file->getRealPath()),
                $file->getClientOriginalName()
            )->post('https://uguu.se/upload.php');

            if ($response->successful()) {
                $data = $response->json();
                if (isset($data['files'][0]['url'])) {
                    return $data['files'][0]['url'];
                }
            }
        } catch (\Exception $e) {
            Log::error('Uguu.se upload failed: ' . $e->getMessage());
        }

        // 3. Backup Fallback: Store locally if external uploads are completely unavailable
        try {
            Log::warning('External image hosts failed, saving to local public disk instead.');
            $path = $file->store($fallbackFolder, 'public');
            return '/storage/' . $path;
        } catch (\Exception $e) {
            Log::error('Local fallback storage failed: ' . $e->getMessage());
        }

        return null;
    }
}
