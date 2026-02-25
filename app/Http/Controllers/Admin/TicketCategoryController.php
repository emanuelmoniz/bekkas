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

    public function index()
    {
        $this->ensureAdmin();

        $categories = TicketCategory::with('translations')->get();

        return view('admin.ticket-categories.index', compact('categories'));
    }

    public function create()
    {
        $this->ensureAdmin();

        return view('admin.ticket-categories.create');
    }

    public function store(Request $request)
    {
        $this->ensureAdmin();

        $request->validate([
            'name.pt-PT' => 'required|string|max:255',
            'name.en-UK' => 'required|string|max:255',
        ]);

        $category = TicketCategory::create([
            'active' => true,
        ]);

        foreach (Locale::activeCodes() as $locale) {
            TicketCategoryTranslation::create([
                'ticket_category_id' => $category->id,
                'locale' => $locale,
                'name' => $request->name[$locale],
            ]);
        }

        return redirect()->route('admin.ticket-categories.index');
    }

    public function edit(TicketCategory $ticketCategory)
    {
        $this->ensureAdmin();

        return view('admin.ticket-categories.edit', [
            'category' => $ticketCategory->load('translations'),
        ]);
    }

    public function update(Request $request, TicketCategory $ticketCategory)
    {
        $this->ensureAdmin();

        $request->validate([
            'name.pt-PT' => 'required|string|max:255',
            'name.en-UK' => 'required|string|max:255',
            'active' => 'nullable|boolean',
        ]);

        $ticketCategory->update([
            'active' => (bool) $request->active,
        ]);

        foreach (Locale::activeCodes() as $locale) {
            TicketCategoryTranslation::updateOrCreate(
                [
                    'ticket_category_id' => $ticketCategory->id,
                    'locale' => $locale,
                ],
                [
                    'name' => $request->name[$locale],
                ]
            );
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
