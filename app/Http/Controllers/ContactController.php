<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Mail\ContactMessage;
use App\Mail\ContactConfirmation;

class ContactController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'message' => 'required|string|max:5000',
        ]);

        $locale = app()->getLocale();

        Mail::to('info@bekkas.pt')->locale($locale)->send(new ContactMessage(
            $validated['name'],
            $validated['email'],
            $validated['message']
        ));

        Mail::to($validated['email'])->locale($locale)->send(new ContactConfirmation(
            $validated['name']
        ));

        return back()->with('success', t('contact.success_message') ?: 'Thank you for your message! We will get back to you soon.');
    }
}
