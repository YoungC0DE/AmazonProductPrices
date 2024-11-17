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
        self::STATUS_PENDING => 'Pending',
        self::STATUS_FINISHED => 'Finished',
        self::STATUS_RUNNING => 'Running',
        self::STATUS_ERROR => 'Error',
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'request',
        'status'
    ];
}
