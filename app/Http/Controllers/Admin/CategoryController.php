<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\CategoryTranslation;
use App\Models\Locale;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = Category::with(['translations', 'parent.translations'])->get();

        return view('admin.categories.index', compact('categories'));
    }

    public function create()
    {
        $categories = Category::with('translations')->get();

        return view('admin.categories.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $category = Category::create([
            'parent_id' => $request->parent_id,
        ]);

        foreach (Locale::activeCodes() as $locale) {
            CategoryTranslation::create([
                'category_id' => $category->id,
                'locale' => $locale,
                'name' => $request->input("name.$locale"),
            ]);
        }

        return redirect()->route('admin.categories.index');
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
        $category->update([
            'parent_id' => $request->parent_id,
        ]);

        foreach (Locale::activeCodes() as $locale) {
            $category->translations()
                ->updateOrCreate(
                    ['locale' => $locale],
                    ['name' => $request->input("name.$locale")]
                );
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
