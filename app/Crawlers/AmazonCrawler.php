<?php

namespace App\Crawlers;

use App\Repositories\TicketRepository;
use Facebook\WebDriver\WebDriverBy;
use Facebook\WebDriver\WebDriverSelect;
use Illuminate\Support\Facades\Log;

class AmazonCrawler extends AbstractCrawler
{
    protected array $response = [];

    protected const URL = 'https://www.amazon.com.br/';

    protected const MAX_LIST_ITEMS = 5;

    protected const INPUT_SEARCH_ID = 'twotabsearchtextbox';
    protected const SEARCH_BTN_ID = 'nav-search-submit-button';

    protected const ITEM_SELETOR = "[data-csa-c-posx='%s']";
    protected const IMAGE_SELETOR = '.s-image';
    protected const TITLE_SELETOR = '[data-cy="title-recipe"] h2 span';
    protected const DESCRIPTION_SELETOR = '[data-cy="title-recipe"] a span';
    protected const RATING_SELETOR = '[data-cy="reviews-block"] div a:nth-child(2)';
    protected const PRICE_SELETOR = '[data-cy="price-recipe"] [aria-hidden="true"]';
    protected const DELIVERY_SELETOR = '[data-cy="delivery-recipe"] span:nth-child(1)';
    protected const SORT_SELETOR = 's-result-sort-select';

    protected const MAPPING_SORT_BY = [
        'priceAscending' => 'price-asc-rank',
        'priceDescending' => 'price-desc-rank',
        'relevance' => 'relevanceblender'
    ];

    /**
     * @param array $ticketData
     * 
     * @return array
     */
    public function process(array $ticketData): array
    {
        $this->driver->get(self::URL);

        sleep(2);

        $this->driver->manage()->window()->maximize();

        $this->searchItems($ticketData);

        sleep(2);

        $this->driver->quit();

        return $this->response;
    }

    /**
     * @param array $ticketData
     * 
     * @return void
     */
    protected function searchItems(array $ticketData): void
    {
        $searchQuery = $ticketData['requestSettings']['searchQuery'] ?? '';

        if (empty($searchQuery)) {
            throw new \Exception('Invalid ticket data. Missing searchQuery.');
        }

        sleep(2);

        $this->driver->findElement(WebDriverBy::id(self::INPUT_SEARCH_ID))
            ->sendKeys($searchQuery);

        sleep(1);

        $this->driver->findElement(WebDriverBy::id(self::SEARCH_BTN_ID))
            ->click();

        sleep(2);
        
        Log::info('Searching for items...');

        $sortBy = $ticketData['filters']['sortBy'] ?? '';

        if (
            !empty($sortBy) || 
            in_array($sortBy, self::MAPPING_SORT_BY)
        ) {
            sleep(2);

            $selectElement = $this->driver->findElement(WebDriverBy::id(self::SORT_SELETOR));

            sleep(1);
    
            (new WebDriverSelect($selectElement))
                ->selectByValue(self::MAPPING_SORT_BY[$sortBy]);

            Log::info('Filter applied.');

            sleep(2);
        }

        for ($count = 1; $count <= self::MAX_LIST_ITEMS; $count++) {
            $seletor = sprintf(self::ITEM_SELETOR, $count);
            $item = $this->driver->findElement(
                WebDriverBy::cssSelector($seletor)
            );

            Log::info("Getting data from item $count ...");

            sleep(1);

            $this->parseItems($item);

            sleep(1);
        }
    }

    /**
     * @param \Facebook\WebDriver\Remote\RemoteWebElement $item
     * 
     * @return void
     */
    protected function parseItems($item): void
    {
        $this->response[] = [
            'image' => $this->getElementAttributeValue($item, self::IMAGE_SELETOR, 'src', false),
            'title' => $this->getElementText($item, self::TITLE_SELETOR),
            'description' => $this->getElementText($item, self::DESCRIPTION_SELETOR),
            'rating' => $this->getElementAttributeValue($item, self::RATING_SELETOR, 'aria-label'),
            'price' => $this->getElementText($item, self::PRICE_SELETOR, true, 'price'),
            'deliveryInfo' => $this->getElementAttributeValue($item, self::DELIVERY_SELETOR, 'aria-label')
        ];

        Log::info("Data saved.");
    }
}
