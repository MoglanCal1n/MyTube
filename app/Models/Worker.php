<?php

namespace App\Models;

class Worker extends User
{
    protected static function booted(): void
    {
        static::addGlobalScope('worker', function ($query) {
            $query->where('user_type', self::TYPE_USER);
        });
    }
}

