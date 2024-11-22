<?php

namespace Tests\App\Http\Controllers;

use App\Http\Controllers\TicketController;
use App\Http\Requests\Tickets\CreateRequest;
use App\Models\Tickets;
use App\Repositories\TicketRepository;
use Tests\TestCase;

class TicketControllerTest extends TestCase
{
    protected TicketRepository $ticketRepository;
    protected TicketController $ticketController;
    protected CreateRequest $ticketRequest;

    protected function setUp(): void
    {
        $this->ticketRepository = new TicketRepository(
            (new Tickets)
        );

        $this->ticketController = new TicketController($this->ticketRepository);

        $this->ticketRequest = new CreateRequest();

        parent::setUp();
    }

    /**
     * @return void
     * @throws \Exception
     */
    public function testGetInvalidTicket(): void
    {
        $result = $this->ticketController
            ->get('673f6c3e7956f4f6e10b7b22')
            ->getData(true);

        $this->assertIsArray($result);
        $this->assertEmpty($result['item']);
    }

    /**
     * @return void
     * @throws \Exception
     */
    public function testCreateTicket(): void
    {
        $mockData = [
            'requestSettings' => [
                'platform' => 'AMAZON',
                'searchQuery' => 'Nike SB Sneakers'
            ],
            'filters' => [
                'sortBy' => 'priceAscending',
                'ratingAbove' => 4
            ]
        ];

        $request = $this->ticketRequest
            ->replace($mockData);

        $response = $this->ticketController
            ->create($request);

        $statusCode = $response->status();
        $this->assertEquals(202, $statusCode);

        $responseData = json_decode($response->content(), true);
        $this->assertArrayHasKey('item', $responseData);
    }
}
