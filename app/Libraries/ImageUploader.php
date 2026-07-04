<?php

namespace App\Libraries;

use Config\Services;

class ImageUploader
{
    /**
     * Upload and resize image. Returns relative path from public/ or null on failure.
     */
    public static function upload(string $fieldName, string $folder = 'uploads', int $maxWidth = 1200): ?string
    {
        $request = service('request');
        $file    = $request->getFile($fieldName);

        if (! $file || ! $file->isValid() || $file->hasMoved()) {
            return null;
        }

        $allowed = ['image/jpeg', 'image/png', 'image/webp', 'image/gif'];
        if (! in_array($file->getMimeType(), $allowed, true)) {
            return null;
        }

        $dir = FCPATH . $folder;
        if (! is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        $newName = $file->getRandomName();
        $file->move($dir, $newName);

        $path = $dir . DIRECTORY_SEPARATOR . $newName;

        try {
            $image = Services::image();
            $image->withFile($path)
                ->resize($maxWidth, $maxWidth, true, 'width')
                ->save($path, 80);
        } catch (\Throwable $e) {
            // Keep original if resize fails
        }

        return $folder . '/' . $newName;
    }

    public static function delete(?string $relativePath): void
    {
        if (! $relativePath) {
            return;
        }

        $full = FCPATH . $relativePath;
        if (is_file($full)) {
            unlink($full);
        }
    }
}
