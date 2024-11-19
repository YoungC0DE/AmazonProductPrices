<?php

namespace App\Crawlers;

use App\Services\CrawlerService;
use Facebook\WebDriver\WebDriverBy;

class AmazonCrawler
{
    protected array $response = [];

    public function process()
    {
        $driver = CrawlerService::getDriver();

        $driver->get('https://www.amazon.com.br/');

        sleep(2);

        $driver->manage()->window()->maximize();

        $driver->findElement(WebDriverBy::id('twotabsearchtextbox'))
            ->sendKeys('TENIS NIKE');

        $driver->findElement(WebDriverBy::id('nav-search-submit-button'))
            ->click();

        sleep(2);

        $contentPage = $driver->getPageSource();
        if (str_contains($contentPage, 'Nenhum resultado para')) {
            $driver->quit();

            return [];
        }

        $this->response = [
            'product' => '',
            'description' => '',
            'price' => '',
        ];

        return $this;
    }
}
