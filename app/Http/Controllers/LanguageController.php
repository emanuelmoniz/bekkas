<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class LanguageController extends Controller
{
    public function switch($locale)
    {
        // Validate the locale is supported
        if (!array_key_exists($locale, config('app.locales'))) {
            abort(404);
        }

        // Store in session
        session(['locale' => $locale]);

        // Set the application locale
        app()->setLocale($locale);

        // Redirect back to previous page or home
        return redirect()->back();
    }
}
