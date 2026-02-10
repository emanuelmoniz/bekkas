<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductPhoto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProductPhotoController extends Controller
{
    public function store(Request $request, Product $product)
    {
        $request->validate([
            'photos.*' => 'required|image|max:4096',
        ]);

        $hasPrimary = $product->photos()->where('is_primary', true)->exists();

        foreach ($request->file('photos', []) as $index => $file) {
            $path = $file->store('products', 'public');

            $product->photos()->create([
                'path' => $path,
                'is_primary' => ! $hasPrimary && $index === 0,
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

    public function destroy(ProductPhoto $photo)
    {
        Storage::disk('public')->delete($photo->path);
        $photo->delete();

        return back();
    }
}
