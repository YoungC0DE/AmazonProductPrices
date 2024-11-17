<?php

namespace Tests\App\Http\Controllers;

use App\Http\Controllers\TicketController;
use App\Models\Tickets;
use App\Repositories\TicketRepository;
use Tests\TestCase;

class TicketControllerTest extends TestCase
{
    public function test_get_with_invalid_id(): void
    {
        $repository = new TicketRepository(
            (new Tickets)
        );

        $controller = new TicketController($repository);
        $result = $controller->get('123')->getData(true);

        $this->assertIsArray($result);
        $this->assertEmpty($result['item']);
    }
}