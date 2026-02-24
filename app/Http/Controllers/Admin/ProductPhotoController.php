<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductPhoto;
use App\Services\ImageThumbnailService;
use Illuminate\Http\Request;

class ProductPhotoController extends Controller
{
    public function store(Request $request, Product $product, ImageThumbnailService $thumbnails)
    {
        $request->validate([
            'photos.*' => 'required|image|max:20480',
        ]);

        $hasPrimary = $product->photos()->where('is_primary', true)->exists();

        foreach ($request->file('photos', []) as $index => $file) {
            $stored = $thumbnails->store($file, 'products');

            $product->photos()->create([
                'path'          => $stored['path'],
                'original_path' => $stored['original_path'],
                'is_primary'    => ! $hasPrimary && $index === 0,
            ]);
        }

        return back();
    }

    public function makePrimary(ProductPhoto $photo)
    {
        $photo->product->photos()->update(['is_primary' => false]);
        $photo->update(['is_primary' => true]);

        return back();
    }

    public function destroy(ProductPhoto $photo, ImageThumbnailService $thumbnails)
    {
        $thumbnails->delete($photo->path, $photo->original_path);
        $photo->delete();

        return back();
    }
}

