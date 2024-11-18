<?php

namespace App\Console\Commands;

use App\Models\Tickets;
use App\Repositories\TicketRepository;
use App\Services\BeanstalkService;
use Pheanstalk\Values\TubeName;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Pheanstalk\Pheanstalk;

class ticketQueue extends Command
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

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $queue = BeanstalkService::getInstance();
        $queue->useTube(new TubeName(BeanstalkService::TICKET_QUEUE_TUBE));

        try {
            $repository = new TicketRepository(
                new Tickets()
            );

            $ticketId = $this->option('ticketId') ?? '';

            $context = [
                'queue' => $this->signature,
            ];

            Log::info('Looking for pending tickets...', $context);

            $tickets = $repository->getPendingTickets($ticketId) ?? [];

            Log::info('pending tickets found: '. count($tickets), $context);

            if (!empty($tickets)) {
                foreach($tickets as $ticket) {
                    $this->sendToQueue($ticket);
                }
            }

            Log::info('Process finalized.', $context);
        } catch (\Exception $error) {
            Log::error($error, $context);
        }
    }

    /**
     * @param array $ticket
     * @return void
     */
    public function sendToQueue(array $ticket)
    {
        $queue = BeanstalkService::getInstance(env('BEANSTALK_HOST'));
        $queue->useTube(new TubeName(BeanstalkService::TICKET_PROCESS_TUBE));
        $queue->put(
            json_encode($ticket),
            Pheanstalk::DEFAULT_PRIORITY,
            Pheanstalk::DEFAULT_DELAY,
            BeanstalkService::TTL_ONE_HOUR,
        );
    }
}
