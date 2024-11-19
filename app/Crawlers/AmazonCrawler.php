<?php

namespace App\Crawlers;

use Facebook\WebDriver\WebDriverBy;
use Facebook\WebDriver\WebDriverSelect;

class AmazonCrawler extends AbstractCrawler
{
    protected array $response = [];

    public function process()
    {
        $this->driver->get('https://www.amazon.com.br/');

        sleep(2);
        // aqui talvez seja preciso por um wait loader pra esperar a pagina carregar

        $this->driver->manage()->window()->maximize();

        $this->searchItems();
        $this->parseItems();

        $this->driver->quit();

        return $this->response;
    }

    protected function searchItems()
    {
        $this->driver->findElement(WebDriverBy::id('twotabsearchtextbox'))
            ->sendKeys('TENIS NIKE');

        $this->driver->findElement(WebDriverBy::id('nav-search-submit-button'))
            ->click();

        sleep(2);

        $selectElement = $this->driver->findElement(WebDriverBy::id('s-result-sort-select'));

        (new WebDriverSelect($selectElement))
            ->selectByValue('review-rank');

        $contentPage = $this->driver->getPageSource();
        if (str_contains($contentPage, 'Nenhum resultado para')) {
            return $this->response = [];
        }
    }

    protected function parseItems()
    {
        // parseia os itens e retorna

        $this->response = [
            'product' => '',
            'description' => '',
            'price' => ''
        ];
    }
}
