<?php

namespace App\Http\Controllers;

use App\Models\Locale;

class LanguageController extends Controller
{
    public function switch($locale)
    {
        // Validate the locale is supported and active in the database
        $exists = Locale::where('code', $locale)->where('is_active', true)->exists();
        if (! $exists) {
            abort(404);
        }

        // Store in session
        session(['locale' => $locale]);

        // Set the application locale
        app()->setLocale($locale);

        // If user is authenticated, update their language preference
        if (auth()->check()) {
            $user = auth()->user();
            $user->language = $locale;
            $user->save();
        }

        // Redirect back to previous page or home
        return redirect()->back();
    }
}
