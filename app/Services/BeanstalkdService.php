<?php

namespace App\Services;

use Pheanstalk\Pheanstalk;

class BeanstalkService
{
    // TUBES
    public const TICKET_PROCESS_TUBE = 'ticket-process';
    public const TICKET_QUEUE_TUBE = 'ticket-queue';

    // TTL CACHE
    public const TTL_ONE_MINUTE = 60;
    public const TTL_TEN_MINUTE = 10;
    public const TTL_ONE_HOUR = 3600;
    public const TTL_ONE_DAY = 86400;
    public const TTL_ONE_MONTH = 259200;

    /**
     * @var Pheanstalk
     */
    protected static $localInstance;

    /**
     * @param $host
     * @param bool $fresh
     * @return Pheanstalk
     */
    public static function getInstance($host, bool $fresh = false)
    {
        if (!empty($fresh)) {
            return Pheanstalk::create($host);
        }

        if (null === static::$localInstance) {
            static::$localInstance = Pheanstalk::create($host);
        }

        return static::$localInstance;
    }
}