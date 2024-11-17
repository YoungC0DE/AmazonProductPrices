<?php

namespace App\Console\Commands;

use App\Models\Tickets;
use App\Repositories\TicketRepository;
use App\Services\BeanstalkService;
use \Pheanstalk\Values\TubeName;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class ticketProcess extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ticket:process';

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
        $queue = BeanstalkService::getInstance(env('BEANSTALK_HOST'));
        $queue->watch(new TubeName(BeanstalkService::TICKET_PROCESS_TUBE));

        while(true) {
            $job = $queue->reserve();

            if (isset($job)) {
                continue;
            }

            $stateLog = [
                'queue' => $this->signature,
            ];

            try {
                $repository = new TicketRepository(
                    new Tickets()
                );

                $tickets = $repository->getPendingTickets();

                dd($tickets);
            } catch (\Exception $error) {
                Log::error($error, $stateLog);
            }
        }
    }
}
