<?php

namespace App\Http\Controllers;

class LanguageController extends Controller
{
    public function switch($locale)
    {
        // Validate the locale is supported
        if (! array_key_exists($locale, config('app.locales'))) {
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
