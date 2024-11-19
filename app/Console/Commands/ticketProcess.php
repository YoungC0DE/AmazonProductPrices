<?php

namespace App\Console\Commands;

use App\Crawlers\AmazonCrawler;
use App\Factories\CrawlerFactory;
use App\Services\BeanstalkService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Pheanstalk\Values\TubeName;

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
                'crawlerType' => $jobData['crawlerType'] ?? ''
            ];

            try {
                Log::info('Starting crawler...');

                $type = 'Amazon';
                $crawler = CrawlerFactory::create($type);

                $response = $crawler->process();
                dd($response);

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
