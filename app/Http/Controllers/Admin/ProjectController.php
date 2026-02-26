<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Locale;
use App\Models\Material;
use App\Models\Project;
use App\Models\ProjectTranslation;
use App\Services\ImageThumbnailService;
use Illuminate\Http\Request;

class ProjectController extends Controller
{
    public function index(Request $request)
    {
        $query = Project::with('translations');

        // NAME / TITLE (translation)
        if ($request->filled('name')) {
            $query->whereHas('translations', function ($q) use ($request) {
                $q->where('name', 'like', '%'.trim($request->name).'%');
            });
        }

        // PRODUCTION DATE range
        if ($request->filled('production_date_start')) {
            $query->where('production_date', '>=', $request->production_date_start);
        }
        if ($request->filled('production_date_end')) {
            $query->where('production_date', '<=', $request->production_date_end);
        }

        // MATERIAL
        if ($request->filled('material_id')) {
            $query->whereHas('materials', function ($q) use ($request) {
                $q->where('materials.id', $request->material_id);
            });
        }

        // ACTIVE
        if ($request->filled('is_active')) {
            $query->where('is_active', (bool) $request->is_active);
        }

        // FEATURED
        if ($request->filled('is_featured')) {
            $query->where('is_featured', (bool) $request->is_featured);
        }

        $projects = $query->paginate(20)->withQueryString();

        $materials = Material::with('translations')->get();

        return view('admin.projects.index', compact(
            'projects',
            'materials'
        ));
    }

    public function create()
    {
        $materials = Material::with('translations')->get();

        return view('admin.projects.create', compact('materials'));
    }

    public function store(Request $request)
    {
        $defaultLocale = Locale::defaultLocale()?->code ?? 'en-UK';
        $nameRules = [];
        foreach (Locale::activeCodes() as $locale) {
            $nameRules["name.$locale"] = $locale === $defaultLocale ? 'required|string|max:255' : 'nullable|string|max:255';
        }
        $request->validate($nameRules);

        $project = Project::create([
            'production_date' => $request->production_date,
            'execution_time' => $request->execution_time,
            'width' => $request->width ?: null,
            'length' => $request->length ?: null,
            'height' => $request->height ?: null,
            'weight' => $request->weight,
            'is_active' => $request->boolean('is_active', true),
            'is_featured' => $request->boolean('is_featured'),
        ]);

        foreach (Locale::activeList() as $locale => $locLabel) {
            $nameValue = $request->input("name.$locale");
            if (!empty($nameValue)) {
                ProjectTranslation::create([
                    'project_id' => $project->id,
                    'locale' => $locale,
                    'name' => $nameValue,
                    'description' => $request->input("description.$locale"),
                ]);
            }
        }

        $project->materials()->sync($request->materials ?? []);

        return redirect()->route('admin.projects.index');
    }

    public function show(Project $project)
    {
        $project->load(['translations', 'materials', 'photos']);
        return view('admin.projects.show', compact('project'));
    }

    public function edit(Project $project)
    {
        $project->load(['translations', 'materials']);
        $materials = Material::with('translations')->get();

        return view('admin.projects.edit', compact(
            'project',
            'materials'
        ));
    }

    public function update(Request $request, Project $project)
    {
        $defaultLocale = Locale::defaultLocale()?->code ?? 'en-UK';
        $nameRules = [];
        foreach (Locale::activeCodes() as $locale) {
            $nameRules["name.$locale"] = $locale === $defaultLocale ? 'required|string|max:255' : 'nullable|string|max:255';
        }
        $request->validate($nameRules);

        $project->update([
            'production_date' => $request->production_date,
            'execution_time' => $request->execution_time,
            'width' => $request->width ?: null,
            'length' => $request->length ?: null,
            'height' => $request->height ?: null,
            'weight' => $request->weight,
            'is_active' => $request->boolean('is_active'),
            'is_featured' => $request->boolean('is_featured'),
        ]);

        foreach (Locale::activeList() as $locale => $locLabel) {
            $nameValue = $request->input("name.$locale");
            if (!empty($nameValue)) {
                $project->translations()
                    ->updateOrCreate(
                        ['locale' => $locale],
                        [
                            'name' => $nameValue,
                            'description' => $request->input("description.$locale"),
                        ]
                    );
            }
        }

        $project->materials()->sync($request->materials ?? []);

        return redirect()->route('admin.projects.index');
    }

    public function destroy(Project $project, ImageThumbnailService $thumbnails)
    {
        // delete both thumbnail and original – the service handles a
        // null original_path gracefully for legacy rows.
        foreach ($project->photos as $photo) {
            $thumbnails->delete($photo->path, $photo->original_path);
        }

        $project->delete();

        return redirect()->route('admin.projects.index');
    }
}
