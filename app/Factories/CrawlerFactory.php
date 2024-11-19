<?php

namespace App\Factories;

use App\Exceptions\InvalidClassException;

class CrawlerFactory
{
    protected const BASE_CLASS = 'App\Crawlers\%sCrawler';
    public static function create(string $type)
    {
        $type = ucfirst(strtolower($type));

        $class = sprintf(self::BASE_CLASS, $type);
        if (class_exists($class)) {
            return new $class();
        }

        throw new InvalidClassException('Crawler class not found');
    }
}
