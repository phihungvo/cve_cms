<?php declare(strict_types=1);

namespace App\Domains\Position\Command;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Redis;

class CleanUpdateCityJobs extends Command
{
    protected $signature = 'position:clean-update-city-jobs';
    protected $description = 'Remove UpdateCity jobs from the queue while preserving other jobs.';

    public function handle()
    {
        $queue = 'platform-queues:platform';
        $tempQueue = 'platform-queues:platform:temp';
        $updateCityJobName = 'App\\Domains\\Position\\Job\\UpdateCity';

        $totalJobs = Redis::connection()->llen($queue);
        $this->info("Total jobs in queue: $totalJobs");

        $processed = 0;
        $kept = 0;
        $removed = 0;

        while ($job = Redis::connection()->lpop($queue)) {
            $processed++;

            $payload = json_decode($job, true);
            $isUpdateCityJob = isset($payload['displayName']) && $payload['displayName'] === $updateCityJobName;

            if (!$isUpdateCityJob) {
                Redis::connection()->rpush($tempQueue, $job);
                $kept++;
            } else {
                $removed++;
            }

            if ($processed % 10000 === 0) {
                $this->info("Processed: $processed, Kept: $kept, Removed: $removed");
            }
        }

        Redis::connection()->rename($tempQueue, $queue);

        $this->info("Completed. Processed: $processed, Kept: $kept, Removed: $removed");
    }
}