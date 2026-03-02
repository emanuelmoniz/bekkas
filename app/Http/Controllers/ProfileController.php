<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        return view('profile.edit', [
            'user' => $request->user(),
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $validated = $request->safe()->except('email_confirmation');

        $request->user()->fill($validated);

        if ($request->user()->isDirty('email')) {
            $request->user()->email_verified_at = null;
        }

        $request->user()->save();

        return Redirect::route('profile.edit')->with('success', t('profile.updated_success') ?: 'Profile updated successfully!');
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        // Validate password (errors put in `userDeletion` bag for the modal)
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        // Audit: record delete attempt
        Log::info('User delete requested', [
            'user_id' => $user->id,
            'email' => $user->email,
            'ip' => $request->ip(),
        ]);

        // Defensive: reassign tickets where this user is the `created_by` to the ticket owner
        // so deleting the user won't fail due to FK constraints. This is safe and preserves
        // ticket ownership/history (creator becomes the ticket owner when appropriate).
        DB::table('tickets')
            ->where('created_by', $user->id)
            ->update(['created_by' => DB::raw('user_id')]);

        // Perform deletion inside a try/catch so failures are logged and surfaced
        try {
            Auth::logout();

            $user->delete();

            $request->session()->invalidate();
            $request->session()->regenerateToken();

            Log::info('User deleted', ['user_id' => $user->id, 'email' => $user->email]);

            return Redirect::to('/');
        } catch (\Throwable $e) {
            Log::error('User deletion failed', ['user_id' => $user->id, 'error' => $e->getMessage()]);

            return Redirect::route('profile.edit')->withErrors(['userDeletion' => ['password' => t('profile.delete_failed') ?: 'Account deletion failed — please contact support.']]);
        }
    }

    /**
     * Send a signed, time-limited deletion confirmation link to the user's email.
     */
    public function sendDeletionLink(Request $request): RedirectResponse
    {
        $user = $request->user();

        // Only meaningful for social-only or password-less users, but harmless otherwise.
        Log::info('Account deletion link requested (user)', ['user_id' => $user->id, 'email' => $user->email]);

        $user->notify(new \App\Notifications\DeleteAccountNotification);

        return back()->with('status', 'deletion-link-sent');
    }

    /**
     * Show a confirmation page for signed deletion links. The signed link is required
     * but the destructive action is performed only via POST to avoid link scanners
     * accidentally triggering account deletion.
     */
    public function confirmDeletion(Request $request, $id)
    {
        if (! $request->hasValidSignature()) {
            abort(403);
        }

        $user = User::findOrFail($id);

        if (! $request->has('hash') || sha1($user->getEmailForVerification()) !== $request->query('hash')) {
            abort(403);
        }

        // Render a confirmation view with a POST form (prevents automatic link scanners from deleting)
        return view('profile.confirm-delete', ['user' => $user]);
    }

    /**
     * Perform the actual deletion after the user confirms on the confirmation page.
     * This route is POST and still requires the signed URL parameters.
     */
    public function performDeletion(Request $request, $id): RedirectResponse
    {
        if (! $request->hasValidSignature()) {
            abort(403);
        }

        $user = User::findOrFail($id);

        if (! $request->has('hash') || sha1($user->getEmailForVerification()) !== $request->query('hash')) {
            abort(403);
        }

        Log::info('Account deletion confirmed via signed link', ['user_id' => $user->id, 'email' => $user->email, 'ip' => $request->ip()]);

        DB::table('tickets')
            ->where('created_by', $user->id)
            ->update(['created_by' => DB::raw('user_id')]);

        try {
            if (auth()->check() && auth()->id() === $user->id) {
                Auth::logout();

                $request->session()->invalidate();
                $request->session()->regenerateToken();
            }

            $user->delete();

            Log::info('User deleted via signed deletion link', ['user_id' => $user->id, 'email' => $user->email]);

            return Redirect::to('/');
        } catch (\Throwable $e) {
            Log::error('Signed deletion failed', ['user_id' => $user->id, 'error' => $e->getMessage()]);

            return Redirect::to('/')->withErrors(['delete' => t('profile.delete_failed')]);
        }
    }
}
