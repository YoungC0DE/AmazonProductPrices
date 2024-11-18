<?php

namespace App\Console\Commands;

use App\Services\BeanstalkService;
use App\Services\CrawlerService;
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
        $queue = BeanstalkService::getInstance();
        $queue->watch(new TubeName(BeanstalkService::TICKET_PROCESS_TUBE));

        while(true) {
            $job = $queue->reserve();

            if ($job === null) {
                sleep(2);
                continue;
            }

            $jobData = json_decode($job->getData(), true);

            $stateLog = [
                'queue' => $this->signature,
                'ticketId' => $jobData['_id'] ?? '',
                'request' => $jobData['request'] ?? ''
            ];

            try {
                $driver = CrawlerService::getDriver();

                $driver->manage()->window()->maximize();

                $driver->get('https://www.amazon.com.br/');

                sleep(2);

                $driver->quit();

                $queue->delete($job);
            } catch (\Exception $error) {
                $queue->delete($job);
                Log::error('Error processing ticket', 
                    array_merge($stateLog, [
                        'error_message' => $error->getMessage(),
                        'error_trace' => $error->getTraceAsString(),
                    ])
                );
            }
        }
    }
}
