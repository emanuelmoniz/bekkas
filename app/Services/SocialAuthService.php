<?php

namespace App\Services;

use App\Exceptions\SocialAuthException;
use App\Models\SocialAccount;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Str;

class SocialAuthService
{
    /**
     * Handle a provider user (returned by Socialite) and return a local User.
     *
     * Behaviour (generic provider):
     * - If a SocialAccount exists for provider+provider_id -> return linked user
     * - Else if a user exists by email:
     *     - if email unverified -> throw SocialAuthException(UNVERIFIED_EMAIL)
     *     - else create SocialAccount, link and return user
     * - Else -> create user (verified), attach client role, create SocialAccount, return user
     *
     * @param  \Laravel\Socialite\Two\User|object  $providerUser  object must expose getId(), getEmail(), getName(), getAvatar()
     */
    public function findOrCreateUserFromProvider(string $provider, $providerUser): User
    {
        $providerId = (string) data_get($providerUser, 'id') ?? (string) ($providerUser->getId() ?? null);
        $email = data_get($providerUser, 'email') ?? ($providerUser->getEmail() ?? null);
        $name = data_get($providerUser, 'name') ?? ($providerUser->getName() ?? null);
        $avatar = data_get($providerUser, 'avatar') ?? ($providerUser->getAvatar() ?? null);

        if (empty($providerId)) {
            throw new SocialAuthException('No provider id returned from provider', SocialAuthException::NO_EMAIL);
        }

        // 1) existing social account -> return user
        $social = SocialAccount::where('provider', $provider)->where('provider_id', $providerId)->first();

        if ($social && $social->user) {
            return $social->user;
        }

        // 2) if provider returned an email, try to match by email
        if (! empty($email)) {
            $existing = User::where('email', $email)->first();

            if ($existing) {
                // block unverified account (project preference)
                if (empty($existing->email_verified_at)) {
                    throw new SocialAuthException($email, SocialAuthException::UNVERIFIED_EMAIL, $email);
                }

                // create link and return
                $this->createSocialAccount($existing->id, $provider, $providerId, $avatar);

                return $existing;
            }
        }

        // 3) create new user (email may be null) — treat provider email as verified
        $user = User::create([
            'name' => $name ?: 'User '.Str::random(6),
            'email' => $email,
            'password' => \Hash::make(Str::random(32)),
            'is_active' => true,
            'language' => app()->getLocale(),
            'email_verified_at' => $email ? Carbon::now() : null,
        ]);

        // Debug: ensure created user's email_verified_at was persisted (helps unit tests assert behavior)
        if (empty($user->email_verified_at)) {
            \Illuminate\Support\Facades\Log::warning('SocialAuthService: created user has null email_verified_at', ['email' => $email]);
        } else {
            \Illuminate\Support\Facades\Log::debug('SocialAuthService: created user email_verified_at', ['email' => $email, 'email_verified_at' => $user->email_verified_at]);
        }

        // attach default "client" role if present
        $clientRole = \App\Models\Role::where('name', 'client')->first();
        if ($clientRole) {
            $user->roles()->attach($clientRole->id);
        }

        $this->createSocialAccount($user->id, $provider, $providerId, $avatar);

        return $user;
    }

    protected function createSocialAccount(int $userId, string $provider, string $providerId, ?string $avatar = null): SocialAccount
    {
        // Normalize avatar: if provider returns a data URI, decode and store it on the public disk
        $avatarPath = null;

        if (! empty($avatar) && is_string($avatar)) {
            // data URI (base64) — save to storage and keep a small path in DB
            if (\Illuminate\Support\Str::startsWith($avatar, 'data:')) {
                if (preg_match('/^data:(image\/[^;]+);base64,(.*)$/', $avatar, $m)) {
                    $mime = $m[1];
                    $data = base64_decode($m[2]);
                    $ext = explode('/', $mime)[1] ?? 'jpg';
                    if ($ext === 'jpeg') {
                        $ext = 'jpg';
                    }

                    $filename = 'avatars/social/'.strtolower($provider).'_'.$providerId.'_'.\Illuminate\Support\Str::random(8).'.'.$ext;
                    \Illuminate\Support\Facades\Storage::disk('public')->put($filename, $data);
                    $avatarPath = '/storage/'.$filename;
                }

                // if data URI parsing failed we leave avatarPath null
            } else {
                // non-data URI (likely a URL) — keep as-is but avoid storing extremely long strings
                $avatarPath = strlen($avatar) > 255 ? substr($avatar, 0, 255) : $avatar;
            }
        }

        return SocialAccount::create([
            'user_id' => $userId,
            'provider' => $provider,
            'provider_id' => $providerId,
            'avatar' => $avatarPath,
        ]);
    }

    /**
     * Link a provider account to an existing (authenticated) user.
     * Throws if the provider id is already linked to another user.
     */
    public function linkProviderToUser(User $user, string $provider, $providerUser): SocialAccount
    {
        $providerId = (string) data_get($providerUser, 'id') ?? (string) ($providerUser->getId() ?? null);
        $avatar = data_get($providerUser, 'avatar') ?? ($providerUser->getAvatar() ?? null);

        if (empty($providerId)) {
            throw new SocialAuthException('No provider id returned from provider', SocialAuthException::NO_EMAIL);
        }

        $existing = SocialAccount::where('provider', $provider)->where('provider_id', $providerId)->first();

        if ($existing) {
            if ($existing->user_id === $user->id) {
                return $existing; // already linked to this user
            }

            throw new SocialAuthException('This social account is linked to another user', SocialAuthException::PROVIDER_ALREADY_LINKED);
        }

        return $this->createSocialAccount($user->id, $provider, $providerId, $avatar);
    }

    /**
     * Unlink a provider from a user.
     * Prevent unlinking the last sign-in method (defensive).
     */
    public function unlinkProviderFromUser(User $user, string $provider): void
    {
        $social = SocialAccount::where('user_id', $user->id)->where('provider', $provider)->first();

        if (! $social) {
            return; // nothing to do
        }

        $totalSocial = $user->socialAccounts()->count();

        // Defensive: ensure at least one sign-in method remains (password OR another social account)
        $hasPassword = ! empty($user->password);

        if (! $hasPassword && $totalSocial <= 1) {
            throw new SocialAuthException('Cannot unlink the last sign-in method', SocialAuthException::CANNOT_UNLINK_LAST_AUTH);
        }

        $social->delete();
    }
}
