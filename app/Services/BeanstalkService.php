<?php

namespace App\Services;

use Pheanstalk\Pheanstalk;
use Pheanstalk\Values\TubeName;

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

    private static $instance;

    /**
     * @return Pheanstalk
     */
    public static function getInstance()
    {
        if (null === self::$instance) {
            try {
                self::$instance = Pheanstalk::create(
                    env('BEANSTALK_HOST', 'localhost'),
                    env('BEANSTALK_PORT', 11300)
                );
            } catch (\Exception $e) {
                throw new \RuntimeException('Error connecting to Beanstalkd: ' . $e->getMessage());
            }
        }
        return self::$instance;
    }

    /**
     * @param string $tube
     * @param array $data
     * @return void
     */
    public static function dispatchJob(string $tube, array $data)
    {
        $queue = static::$instance;
        $queue->useTube(new TubeName($tube));
        $queue->put(
            json_encode($data),
            Pheanstalk::DEFAULT_PRIORITY,
            Pheanstalk::DEFAULT_DELAY,
            BeanstalkService::TTL_ONE_HOUR,
        );
    }
}