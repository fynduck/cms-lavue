<?php

namespace Modules\Search\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Modules\Search\Entities\Search;
use Modules\Statistics\Entities\Browser;
use Modules\Statistics\Entities\Ip;
use Modules\Statistics\Services\StatisticService;

class SearchJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    private $query;

    /**
     * Create a new job instance.
     * @param string $query
     */
    public function __construct(string $query)
    {
        $this->query = $query;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $log = Search::where('search', $this->query)
            ->first();

        if ($log) {
            if ($log->updated_at->diffInMinutes(now()->addMinute()) > 1) {
                $log->increment('count');
            }
        } else {
            Search::create(
                [
                    'search' => $this->query,
                    'count'  => 1,
                ]
            );
        }
    }
}
