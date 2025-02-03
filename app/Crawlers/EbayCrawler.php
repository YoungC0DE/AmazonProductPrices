<?php

namespace App\Crawlers;

use App\Repositories\TicketRepository;
use Facebook\WebDriver\WebDriverBy;
use Illuminate\Support\Facades\Log;

class EbayCrawler extends AbstractCrawler
{
    protected array $response = [];

    protected const URL = 'https://www.ebay.com/';

    protected const MAX_LIST_ITEMS = 7;

    protected const INPUT_SEARCH_ID = 'gh-ac';
    protected const SEARCH_BTN_ID = 'gh-search-btn';

    protected const ITEM_SELETOR = "[data-view='mi:1686|iid:%s']";
    protected const IMAGE_SELETOR = 'img';
    protected const TITLE_SELETOR = '.s-item__title span';
    protected const DESCRIPTION_SELETOR = '.s-item__subtitle span';
    protected const RATING_SELETOR = '.s-item__reviews-count span[aria-hidden="true"]';
    protected const PRICE_SELETOR = '.s-item__price';
    protected const DELIVERY_SELETOR = 's-item__shipping.s-item__logisticsCost';
    protected const NULL_RESULT_SELETOR = '.srp-save-null-search';

    protected const MAPPING_SORT_BY = [
        'priceAscending' => '_sop=15',
        'priceDescending' => '_sop=16',
        'relevance' => '_sop=12'
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

        try {
            $result = $this->driver->findElement(
                WebDriverBy::cssSelector(self::NULL_RESULT_SELETOR)
            );

            if (!empty($result->getText())) {
                (new TicketRepository())->updateTicketAsError(
                    $ticketData['_id'],
                    'No results found'
                );

                Log::info('No results found', $ticketData);
                return;
            }
        } catch (\Exception $e) {}

        $sortBy = $ticketData['filters']['sortBy'] ?? '';

        if (
            !empty($sortBy) || 
            in_array($sortBy, self::MAPPING_SORT_BY)
        ) {
            sleep(2);

            $statusFilter = $this->applyUrlFilter(self::MAPPING_SORT_BY[$sortBy] ?? '');

            if (!empty($statusFilter)) {
                Log::info('Filter applied.');
    
                sleep(2);
            }
        }

        for ($count = 2; $count < self::MAX_LIST_ITEMS; $count++) {
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
            'rating' => $this->getElementText($item, self::RATING_SELETOR),
            'price' => $this->getElementText($item, self::PRICE_SELETOR, true, 'price'),
            'deliveryInfo' => $this->getElementAttributeValue($item, self::DELIVERY_SELETOR, 'aria-label')
        ];

        Log::info("Data saved.");
    }

    /**
     * @param string $filter
     * 
     * @return bool
     */
    protected function applyUrlFilter(string $filter): bool
    {
        try {
            $url = $this->driver->getCurrentURL();
            $url .= str_ends_with($url, '&') ? $filter : "&$filter";

            $this->driver->get($url);

            return true;
        } catch(\Exception $exception) {
            Log::error('Filter not applyed.', [$exception]);

            return false;
        }
    }
}
