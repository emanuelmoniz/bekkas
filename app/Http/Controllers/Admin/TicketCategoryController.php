<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Locale;
use App\Models\TicketCategory;
use App\Models\TicketCategoryTranslation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TicketCategoryController extends Controller
{
    protected function ensureAdmin()
    {
        if (! Auth::user()->hasRole('admin')) {
            abort(403);
        }
    }

    public function index(Request $request)
    {
        $this->ensureAdmin();

        $query = TicketCategory::with('translations');

        if ($request->filled('name')) {
            $name = $request->name;
            $query->whereHas('translations', function ($q) use ($name) {
                $q->where('name', 'like', '%'.$name.'%');
            });
        }

        $categories = $query->get();

        return view('admin.ticket-categories.index', compact('categories'));
    }

    public function create()
    {
        $this->ensureAdmin();

        $defaultLocale = Locale::defaultLocale()?->code ?? 'en-UK';

        return view('admin.ticket-categories.create', compact('defaultLocale'));
    }

    public function store(Request $request)
    {
        $this->ensureAdmin();

        \Illuminate\Support\Facades\Log::info('TicketCategory store attempt', [
            'input' => $request->all(),
            'name_array' => $request->input('name'),
        ]);

        $defaultLocale = Locale::defaultLocale()?->code ?? 'en-UK';
        $rules = [
            'slug' => 'required|string|max:100|alpha_dash|unique:ticket_categories,slug',
        ];
        foreach (Locale::activeCodes() as $locale) {
            $rules["name.$locale"] = $locale === $defaultLocale ? 'required|string|max:255' : 'nullable|string|max:255';
            $rules["description.$locale"] = 'nullable|string';
        }
        $request->validate($rules);

        $category = TicketCategory::create([
            'slug' => $request->slug,
            'active' => true,
        ]);

        foreach (Locale::activeCodes() as $locale) {
            $name = $request->input("name.$locale");
            if (! empty($name)) {
                TicketCategoryTranslation::create([
                    'ticket_category_id' => $category->id,
                    'locale' => $locale,
                    'name' => $name,
                    'description' => $request->input("description.$locale"),
                ]);
            }
        }

        return redirect()->route('admin.ticket-categories.index');
    }

    public function show(TicketCategory $ticketCategory)
    {
        $this->ensureAdmin();

        $ticketCategory->load('translations');

        return view('admin.ticket-categories.show', ['category' => $ticketCategory]);
    }

    public function edit(TicketCategory $ticketCategory)
    {
        $this->ensureAdmin();

        $defaultLocale = Locale::defaultLocale()?->code ?? 'en-UK';

        return view('admin.ticket-categories.edit', [
            'category' => $ticketCategory->load('translations'),
            'defaultLocale' => $defaultLocale,
        ]);
    }

    public function update(Request $request, TicketCategory $ticketCategory)
    {
        $this->ensureAdmin();

        $defaultLocale = Locale::defaultLocale()?->code ?? 'en-UK';
        $rules = [
            'active' => 'nullable|boolean',
            'slug' => 'required|string|max:100|alpha_dash|unique:ticket_categories,slug,'.$ticketCategory->id,
        ];
        foreach (Locale::activeCodes() as $locale) {
            $rules["name.$locale"] = $locale === $defaultLocale ? 'required|string|max:255' : 'nullable|string|max:255';
            $rules["description.$locale"] = 'nullable|string';
        }
        $request->validate($rules);

        $ticketCategory->update([
            'slug' => $request->slug,
            'active' => (bool) $request->active,
        ]);

        foreach (Locale::activeCodes() as $locale) {
            $name = $request->input("name.$locale");
            if (! empty($name)) {
                TicketCategoryTranslation::updateOrCreate(
                    [
                        'ticket_category_id' => $ticketCategory->id,
                        'locale' => $locale,
                    ],
                    [
                        'name' => $name,
                        'description' => $request->input("description.$locale"),
                    ]
                );
            }
        }

        return redirect()->route('admin.ticket-categories.index');
    }

    public function destroy(TicketCategory $ticketCategory)
    {
        $this->ensureAdmin();

        if ($ticketCategory->tickets()->exists()) {
            return back()->withErrors(
                'Category is used by tickets and cannot be deleted.'
            );
        }

        $ticketCategory->translations()->delete();
        $ticketCategory->delete();

        return redirect()->route('admin.ticket-categories.index');
    }
}
