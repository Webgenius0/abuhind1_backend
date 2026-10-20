<?php

namespace App\Console\Commands;

use App\Models\FaceScan;
use Illuminate\Console\Command;

class BackfillOverallScores extends Command
{
    protected $signature = 'scores:backfill';

    protected $description = 'Fill overall_score and score_version for existing processed scans';

    public function handle(): int
    {
        $count = 0;

        FaceScan::where('status', 'processed')
            ->whereNull('overall_score')
            ->chunkById(200, function ($scans) use (&$count) {
                foreach ($scans as $scan) {
                    $scan->update([
                        'overall_score' => $scan->calculateOverall(),
                        'score_version' => config('scoring.version'),
                    ]);
                    $count++;
                }
            });

        $this->info("Updated {$count} scans.");

        return self::SUCCESS;
    }
}
