<?php

namespace App\Console\Commands;

use App\Models\Tickets;
use App\Repositories\TicketRepository;
use App\Services\BeanstalkService;
use Pheanstalk\Values\TubeName;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Pheanstalk\Pheanstalk;

class TicketQueue extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ticket:queue {--ticketId=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'execute pendent tickets';

    protected Pheanstalk $queue;
    protected TicketRepository $ticketRepository;

    public function __construct() {
        parent::__construct();

        $this->ticketRepository = new TicketRepository(new Tickets());
        $this->queue = BeanstalkService::getInstance();
    }

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $this->queue->useTube(
            new TubeName(BeanstalkService::TICKET_QUEUE_TUBE)
        );

        try {
            $ticketId = $this->option('ticketId') ?? '';

            $context = [
                'queue' => $this->signature,
            ];

            Log::info('Looking for pending tickets...', $context);

            $tickets = $this->ticketRepository
                ->getPendingTickets($ticketId) ?? [];

            $ticketsCount = count($tickets);
            if (empty($ticketsCount) || $ticketsCount <= 0) {
                Log::info('No pending tickets found.');

                return;
            }

            Log::info("pending tickets found: [$ticketsCount]", $context);

            foreach ($tickets as $ticket) {
                $this->sendToQueue($ticket);
            }

            Log::info('Process finished.', $context);
        } catch (\Exception $error) {
            Log::error($error, $context);
        }
    }

    /**
     * @param array $ticket
     * @return void
     */
    public function sendToQueue(array $ticket): void
    {
        $this->queue->useTube(
            new TubeName(BeanstalkService::TICKET_PROCESS_TUBE)
        );

        $this->queue->put(
            json_encode($ticket),
            Pheanstalk::DEFAULT_PRIORITY,
            Pheanstalk::DEFAULT_DELAY,
            BeanstalkService::TTL_ONE_HOUR,
        );
    }
}
