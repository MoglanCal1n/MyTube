<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    public const TABLE = 'users';
    protected $table = self::TABLE;

    /**
     * User type constants
     */
    public const TYPE_USER = 'user';
    public const TYPE_ADMIN = 'admin';

    /**
     * Available user types
     */
    public const TYPES = [
        self::TYPE_USER => 'User',
        self::TYPE_ADMIN => 'Admin',
    ];

    public const WORKER_ROLES = [
        'developer' => 'Developer',
        'hr' => 'HR-ist',
        'designer' => 'Designer',
    ];

    public const ADMIN_ROLES = [
        'ceo' => 'CEO',
        'cto' => 'CTO',
        'manager' => 'Manager',
    ];

    public function getAvailableRoles(): array
    {
        return match ($this->user_type) {
            self::TYPE_ADMIN => self::ADMIN_ROLES,
            self::TYPE_USER => self::WORKER_ROLES,
            default => [],
        };
    }


    /**
     * Check if user is admin
     */
    public function isAdmin(): bool
    {
        return $this->user_type === self::TYPE_ADMIN;
    }


    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'user_type',
        'profile_picture',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];



    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
}
