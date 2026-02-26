<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\CategoryTranslation;
use App\Models\Locale;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function index(Request $request)
    {
        $query = Category::with(['translations', 'parent.translations']);

        if ($request->filled('name')) {
            $name = $request->name;
            $query->whereHas('translations', function ($q) use ($name) {
                $q->where('name', 'like', '%'.$name.'%');
            });
        }

        $categories = $query->get();

        return view('admin.categories.index', compact('categories'));
    }

    public function create()
    {
        $categories = Category::with('translations')->get();

        return view('admin.categories.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $defaultLocale = Locale::defaultLocale()?->code ?? 'en-UK';
        $nameRules = ['parent_id' => 'nullable|exists:categories,id'];
        foreach (Locale::activeCodes() as $locale) {
            $nameRules["name.$locale"] = $locale === $defaultLocale ? 'required|string|max:255' : 'nullable|string|max:255';
        }
        $request->validate($nameRules);

        $category = Category::create([
            'parent_id' => $request->parent_id,
        ]);

        foreach (Locale::activeCodes() as $locale) {
            $value = $request->input("name.$locale");
            if (!empty($value)) {
                CategoryTranslation::create([
                    'category_id' => $category->id,
                    'locale' => $locale,
                    'name' => $value,
                ]);
            }
        }

        return redirect()->route('admin.categories.index');
    }

    public function show(Category $category)
    {
        $category->load(['translations', 'parent.translations']);

        return view('admin.categories.show', compact('category'));
    }

    public function edit(Category $category)
    {
        $category->load('translations');
        $categories = Category::with('translations')
            ->where('id', '!=', $category->id)
            ->get();

        return view('admin.categories.edit', compact('category', 'categories'));
    }

    public function update(Request $request, Category $category)
    {
        $defaultLocale = Locale::defaultLocale()?->code ?? 'en-UK';
        $nameRules = ['parent_id' => 'nullable|exists:categories,id'];
        foreach (Locale::activeCodes() as $locale) {
            $nameRules["name.$locale"] = $locale === $defaultLocale ? 'required|string|max:255' : 'nullable|string|max:255';
        }
        $request->validate($nameRules);

        $category->update([
            'parent_id' => $request->parent_id,
        ]);

        foreach (Locale::activeCodes() as $locale) {
            $value = $request->input("name.$locale");
            if (!empty($value)) {
                $category->translations()
                    ->updateOrCreate(
                        ['locale' => $locale],
                        ['name' => $value]
                    );
            }
        }

        return redirect()->route('admin.categories.index');
    }

    public function destroy(Category $category)
    {
        if ($category->children()->count() > 0) {
            return redirect()
                ->route('admin.categories.index')
                ->with('error', 'Cannot delete a category that has child categories.');
        }

        $category->delete();

        return redirect()->route('admin.categories.index');
    }
}
