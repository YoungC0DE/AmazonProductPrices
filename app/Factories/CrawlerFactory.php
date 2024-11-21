<?php

namespace App\Factories;

use App\Exceptions\InvalidClassException;

class CrawlerFactory
{
    protected const BASE_CLASS = 'App\Crawlers\%sCrawler';

    /**
     * @param string $type
     * @return mixed
     * @throws InvalidClassException
     */
    public static function create(string $type)
    {
        $crawlerType = self::normalize($type);

        $class = sprintf(self::BASE_CLASS, $crawlerType);
        if (class_exists($class)) {
            return new $class();
        }

        throw new InvalidClassException('Crawler class not found');
    }

    protected static function normalize($crawlerName): string
    {
        if (str_contains($crawlerName, '_')) {
            $cleanedName = ucwords(
                str_replace('_', ' ', strtolower($crawlerName))
            );

            return str_replace(' ', '', $cleanedName);
        }

        return ucfirst(strtolower($crawlerName));
    }
}
