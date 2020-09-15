<?php

namespace App\Jobs;

use App\Models\Job;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Collection;

class GenerateYmlJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private Collection $products;

    /**
     * Create a new job instance.
     *
     * @param Job $job
     * @param Collection $products
     */
    public function __construct(Job $job, Collection $products)
    {
        $this->products = $products;
        $this->job = $job;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        try {
            $this->products->each(
                function (array $item) {
                    $this->job->setStatus(Job::STATUS_IN_PROGRESS)
                        ->save();
//                ...  here is processing of current record
                }
            );

            $this->job->setStatus(Job::STATUS_SUCCESS)
                ->save();
        } catch (Exception $e) {
            $this->job->setError($e->getMessage())
                ->setStatus(Job::STATUS_FAILED)
                ->save();
        }
    }
}
