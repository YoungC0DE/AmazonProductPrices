<?php

namespace App\Factories;

use PHPUnit\Util\InvalidDirectoryException;

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

        throw new InvalidDirectoryException('Crawler class not found');
    }
}
