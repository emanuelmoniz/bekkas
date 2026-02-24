<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Models\ProjectPhoto;
use App\Services\ImageThumbnailService;
use Illuminate\Http\Request;

class ProjectPhotoController extends Controller
{
    public function store(Request $request, Project $project, ImageThumbnailService $thumbnails)
    {
        $request->validate([
            'photos.*' => 'required|image|max:20480',
        ]);

        $hasPrimary = $project->photos()->where('is_primary', true)->exists();

        foreach ($request->file('photos', []) as $index => $file) {
            $stored = $thumbnails->store($file, 'projects');

            $project->photos()->create([
                'path'          => $stored['path'],
                'original_path' => $stored['original_path'],
                'is_primary'    => ! $hasPrimary && $index === 0,
            ]);
        }

        return back();
    }

    public function makePrimary(ProjectPhoto $photo)
    {
        $photo->project->photos()->update(['is_primary' => false]);
        $photo->update(['is_primary' => true]);

        return back();
    }

    public function destroy(ProjectPhoto $photo, ImageThumbnailService $thumbnails)
    {
        $thumbnails->delete($photo->path, $photo->original_path);
        $photo->delete();

        return back();
    }
}

