<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\ImageManager;

class ImageThumbnailService
{
    /**
     * Thumbnail size: shorter side will be capped at this many pixels.
     */
    private const THUMBNAIL_SHORTER_SIDE = 1000;

    private ImageManager $manager;

    public function __construct()
    {
        $this->manager = new ImageManager(new Driver);
    }

    /**
     * Store an uploaded image and generate a 1000px-shorter-side thumbnail.
     *
     * @param  UploadedFile  $file  The uploaded image file.
     * @param  string  $folder  Storage folder, e.g. 'products' or 'projects'.
     * @param  string  $disk  Laravel filesystem disk (default: 'public').
     * @return array{path: string, original_path: string}
     *                                                    `path`          – path to the thumbnail (used in image scrollers).
     *                                                    `original_path` – path to the full-resolution original (for future gallery).
     */
    public function store(UploadedFile $file, string $folder, string $disk = 'public'): array
    {
        $extension = strtolower($file->getClientOriginalExtension()) ?: 'jpg';
        $filename = Str::random(40).'.'.$extension;

        // ── 1. Save original ─────────────────────────────────────────────────
        $originalPath = $folder.'/originals/'.$filename;
        Storage::disk($disk)->put($originalPath, $file->getContent());

        // ── 2. Build thumbnail ────────────────────────────────────────────────
        $image = $this->manager->read($file->getContent());

        $w = $image->width();
        $h = $image->height();
        $shorterSide = min($w, $h);

        if ($shorterSide > self::THUMBNAIL_SHORTER_SIDE) {
            // Scale so the shorter side equals THUMBNAIL_SHORTER_SIDE, preserving aspect ratio.
            if ($w <= $h) {
                // width is the shorter (or equal) side
                $image->scale(width: self::THUMBNAIL_SHORTER_SIDE);
            } else {
                // height is the shorter side
                $image->scale(height: self::THUMBNAIL_SHORTER_SIDE);
            }
        }

        // ── 3. Save thumbnail ─────────────────────────────────────────────────
        $thumbnailPath = $folder.'/'.$filename;
        Storage::disk($disk)->put($thumbnailPath, $image->encodeByExtension($extension));

        return [
            'path' => $thumbnailPath,
            'original_path' => $originalPath,
        ];
    }

    /**
     * Delete both the thumbnail and the original from storage.
     *
     * @param  string  $path  Path to the thumbnail.
     * @param  string|null  $originalPath  Path to the original (nullable for legacy rows).
     */
    public function delete(string $path, ?string $originalPath, string $disk = 'public'): void
    {
        Storage::disk($disk)->delete($path);

        if ($originalPath) {
            Storage::disk($disk)->delete($originalPath);
        }
    }
}
