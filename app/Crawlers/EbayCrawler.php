<?php

namespace App\Crawlers;

class EbayCrawler extends AbstractCrawler
{
    protected array $response = [];

    public function process()
    {
        dd('ebay crawler...');
    }
}
