<?php

namespace App\Crawlers;

use Facebook\WebDriver\WebDriverBy;

class ShopeeCrawler extends AbstractCrawler
{
    protected const URL_BASE = 'https://www.shopee.com.br';

    protected array $response = [];

    public function process(array $ticketData): array
    {
        $this->driver->get(self::URL_BASE);
        sleep(3);

        $pageContent = $this->driver->getPageSource();

        if (preg_match('#Página indisponível#im', $pageContent, $matches)) {
            $this->driver->findElement(WebDriverBy::xpath("//button[contains(text(), 'Entrar')]"))
                ->click();

            sleep(3);
        }


        $this->driver->findElement(WebDriverBy::name('loginKey'))
            ->sendKeys(env('SHOPEE_EMAIL'));

        sleep(1);

        $this->driver->findElement(WebDriverBy::name('password'))
        ->sendKeys(env('SHOPEE_PASSWORD'));

        sleep(1);

        $this->driver->findElement(WebDriverBy::xpath("//button[contains(text(), 'Entre')]"))
            ->click();

        // has captcha here, needs to resolve
        sleep(3);

        $this->searchItems($ticketData);

        $this->driver->quit();

        return $this->response;
    }

    protected function searchItems(array $ticketData)
    {
        dd($ticketData);
    }
}