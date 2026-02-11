<?php

namespace App\Http\Controllers;

use App\Mail\ContactConfirmation;
use App\Mail\ContactMessage;
use App\Rules\Recaptcha;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

class ContactController extends Controller
{
    public function store(Request $request)
    {
        // Build validation rules, but make reCAPTCHA required ONLY when configured.
        $rules = [
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'message' => 'required|string|max:5000',
        ];

        $messages = [
            'name.required' => t('validation.name_required') ?: 'Please enter your name.',
            'email.required' => t('validation.email_required') ?: 'Please enter your email address.',
            'email.email' => t('validation.email_invalid') ?: 'Please enter a valid email address.',
            'message.required' => t('validation.message_required') ?: 'Please enter your message.',
            'message.max' => t('validation.message_max') ?: 'Message cannot exceed 5000 characters.',
        ];

        if (! empty(config('services.recaptcha.secret_key'))) {
            $rules['g-recaptcha-response'] = ['required', new Recaptcha];
            $messages['g-recaptcha-response.required'] = t('validation.recaptcha_required') ?: 'Please verify that you are not a robot.';
        }

        $validator = Validator::make($request->all(), $rules, $messages);

        if ($validator->fails()) {
            return redirect()->to(url()->previous().'#contact')
                ->withErrors($validator)
                ->withInput();
        }

        $validated = $validator->validated();

        // Determine user locale: prefer authenticated user's preference when available,
        // otherwise fall back to the app locale.
        $userLocale = auth()->check() ? (auth()->user()->language ?? app()->getLocale()) : app()->getLocale();

        // Admin emails must always be English (admin surface is English-only)
        Mail::to(config('mail.admin_address', 'info@bekkas.pt'))->locale('en-UK')->queue(new ContactMessage(
            $validated['name'],
            $validated['email'],
            $validated['message']
        ));

        // Confirmation to the sender should respect their configured language where possible
        Mail::to($validated['email'])->locale($userLocale)->queue(new ContactConfirmation(
            $validated['name']
        ));

        return redirect()->to(url()->previous().'#contact')->with('success', t('contact.success_message') ?: 'Thank you for your message! We will get back to you soon.');
    }
}
