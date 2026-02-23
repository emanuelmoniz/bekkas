<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Models\ProjectPhoto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProjectPhotoController extends Controller
{
    public function store(Request $request, Project $project)
    {
        $request->validate([
            'photos.*' => 'required|image|max:4096',
        ]);

        $hasPrimary = $project->photos()->where('is_primary', true)->exists();

        foreach ($request->file('photos', []) as $index => $file) {
            $path = $file->store('projects', 'public');

            $project->photos()->create([
                'path' => $path,
                'is_primary' => ! $hasPrimary && $index === 0,
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

    public function destroy(ProjectPhoto $photo)
    {
        Storage::disk('public')->delete($photo->path);
        $photo->delete();

        return back();
    }
}
