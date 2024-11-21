<?php

namespace App\Models;

class Tickets extends BaseModel
{
    const STATUS_PENDING = 0;
    const STATUS_FINISHED = 1;
    const STATUS_RUNNING = 2;
    const STATUS_ERROR = 3;

    /**
     * @var array<int, string>
     */
    const STATUS_LABEL = [
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
        'ratingAbove'
    ];
}
