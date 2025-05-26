<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Video extends Model
{

    public const TABLE = 'videos';
    protected $table = self::TABLE;
    public const ECONOMIC = 'economic';
    public const IT = 'it';
    public const SCIENCE = 'science';
    public const ART = 'art';
    public const HISTORY = 'history';

    public const GENERAL = 'general';

    public const TYPES = [
        self::ECONOMIC => 'Economic',
        self::IT => 'IT',
        self::SCIENCE => 'Science',
        self::ART => 'Art',
        self::HISTORY => 'History',
        self::GENERAL => 'General',
    ];

    protected $fillable = [
        'name',
        'description',
        'length',
        'type',
        'image',
        'video',
    ];
}
