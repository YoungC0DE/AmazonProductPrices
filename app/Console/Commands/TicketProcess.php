<?php

namespace App\Console\Commands;

use App\Factories\CrawlerFactory;
use App\Models\Products;
use App\Services\BeanstalkService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use MongoDB\BSON\ObjectId;
use Pheanstalk\Pheanstalk;
use Pheanstalk\Values\TubeName;

class TicketProcess extends Command
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

    protected Pheanstalk $queue;

    public function __construct() {
        parent::__construct();

        $this->queue = BeanstalkService::getInstance();
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->queue->watch(
            new TubeName(BeanstalkService::TICKET_PROCESS_TUBE)
        );

        while(true) {
            $job = $this->queue
                ->reserve();

            if ($job === null) {
                sleep(2);

                continue;
            }

            $jobData = json_decode($job->getData(), true);

            $stateLog = [
                'queue' => $this->signature,
                'ticketId' => $jobData['_id'] ?? '',
                'requestSettings' => $jobData['requestSettings'] ?? '',
                'filters' => $jobData['filters'] ?? '',
                'status' => $jobData['status'] ?? ''
            ];

            try {
                Log::info('Starting crawler...');

                $crawlerName = $jobData['requestSettings']['platform'];
                $crawler = CrawlerFactory::create($crawlerName);

                $response = $crawler->process();
                $response = array_merge($response, [
                    'from' => new ObjectId($jobData['_id'])]
                );

                Products::create($response);

                $this->queue->delete($job);
            } catch (\Exception $error) {
                $this->queue->delete($job);
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
