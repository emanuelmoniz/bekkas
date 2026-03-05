<?php

namespace App\Http\Controllers;

use App\Models\Project;

class PortfolioController extends Controller
{
    public function index()
    {
        $projects = Project::with(['photos', 'translations', 'materials.translations'])
            ->where('is_active', true)
            ->orderByDesc('production_date')
            ->get();

        $years = $projects
            ->pluck('production_date')
            ->filter()
            ->map(fn ($d) => $d->year)
            ->unique()
            ->sortDesc()
            ->values();

        $materials = $projects
            ->flatMap(fn ($p) => $p->materials)
            ->unique('id')
            ->sortBy(fn ($m) => optional($m->translation())->name ?? '')
            ->values();

        return view('portfolio.index', compact('projects', 'years', 'materials'));
    }

    public function show(Project $project)
    {
        if (! $project->is_active) {
            abort(404);
        }

        $project->load([
            'photos',
            'translations',
            'materials.translations',
            'categories.translations',
        ]);

        return view('portfolio.show', [
            'project' => $project,
            'relatedCategories' => $project->categories,
        ]);
    }
}
