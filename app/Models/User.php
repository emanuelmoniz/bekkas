<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasFactory, Notifiable;

    /**
     * Override to send a DB-localized verification notification.
     */
    public function sendEmailVerificationNotification()
    {
        $this->notify((new \App\Notifications\VerifyEmailNotification())->locale($this->preferredLocale() ?: app()->getLocale()));
    }

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'name',
        'email',
        'phone',
        'password',
        'is_active',
        'language',
        'email_verified_at',
    ];

    /**
     * The attributes that should be hidden for serialization.
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'is_active' => 'boolean',
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    /**
     * Roles relationship
     */
    public function roles()
    {
        return $this->belongsToMany(Role::class);
    }

    /**
     * Check if user has a role
     */
    public function hasRole(string $role): bool
    {
        return $this->roles()->where('name', $role)->exists();
    }

    /**
     * Return the preferred locale for notifications.
     * Laravel's Notification system will use this when localizing mails.
     */
    public function preferredLocale(): ?string
    {
        return $this->language ?: null;
    }

    /**
     * Check if user is admin
     */
    public function isAdmin(): bool
    {
        return $this->hasRole('admin');
    }

    /**
     * Address relations
     */
    public function addresses()
    {
        return $this->hasMany(Address::class);
    }

    /**
     * Orders relations
     */
    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    /**
     * Favorites relation
     */
    public function favorites()
    {
        return $this->hasMany(Favorite::class);
    }

    /**
     * Social accounts linked to this user (Google, etc)
     */
    public function socialAccounts()
    {
        return $this->hasMany(\App\Models\SocialAccount::class);
    }
}
