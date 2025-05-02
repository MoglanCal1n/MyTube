<?php

namespace App\Models;

class Admin extends User
{
    protected static function booted(): void
    {
        static::addGlobalScope('admin', function ($query) {
            $query->where('user_type', self::TYPE_ADMIN);
        });
    }
}
