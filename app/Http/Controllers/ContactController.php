<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use App\Mail\ContactMessage;
use App\Mail\ContactConfirmation;
use App\Rules\Recaptcha;

class ContactController extends Controller
{
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'message' => 'required|string|max:5000',
            'g-recaptcha-response' => ['required', new Recaptcha],
        ], [
            'g-recaptcha-response.required' => t('contact.recaptcha_required') ?: 'Please verify that you are not a robot.',
        ]);

        if ($validator->fails()) {
            return redirect()->to(url()->previous() . '#contact')
                ->withErrors($validator)
                ->withInput();
        }

        $validated = $validator->validated();

        $locale = app()->getLocale();

        Mail::to('info@bekkas.pt')->locale($locale)->send(new ContactMessage(
            $validated['name'],
            $validated['email'],
            $validated['message']
        ));

        Mail::to($validated['email'])->locale($locale)->send(new ContactConfirmation(
            $validated['name']
        ));

        return redirect()->to(url()->previous() . '#contact')->with('success', t('contact.success_message') ?: 'Thank you for your message! We will get back to you soon.');
    }
}
