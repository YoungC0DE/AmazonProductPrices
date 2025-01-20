<?php

namespace App\Models;

class Tickets extends BaseModel
{
    public const STATUS_PENDING = 0;
    public const STATUS_FINISHED = 1;
    public const STATUS_RUNNING = 2;
    public const STATUS_ERROR = 3;

    /**
     * @var array<int, string>
     */
    public const STATUS_LABEL = [
        self::STATUS_PENDING => 'PENDING',
        self::STATUS_FINISHED => 'FINISHED',
        self::STATUS_RUNNING => 'RUNNING',
        self::STATUS_ERROR => 'ERROR'
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'requestSettings',
        'platform',
        'searchQuery',
        'filters',
        'sortBy',
        'ratingAbove',
        'status',
        'code',
        'name'
    ];
}
