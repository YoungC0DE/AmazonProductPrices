<?php

namespace App\Crawlers;

class MercadoLivreCrawler extends AbstractCrawler
{
    protected array $response = [];

    /**
     * @param array $ticketData
     * @return array
     */
    public function process(array $ticketData): array
    {
        return [];
    }
}
