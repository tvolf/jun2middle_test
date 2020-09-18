<?php

namespace App\Jobs;

use App\Models\Job;
use Bukashk0zzz\YmlGenerator\Generator;
use Bukashk0zzz\YmlGenerator\Model\ShopInfo;
use Bukashk0zzz\YmlGenerator\Settings;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;

class GenerateYmlJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private Collection $products;
    private Job $passedJob;

    /**
     * Create a new job instance.
     *
     * @param Job $job
     * @param Collection $products
     */
    public function __construct(Job $job, Collection $products)
    {
        $this->passedJob = $job;
        $this->products = $products;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {

        try {
            Storage::disk('public')->makeDirectory('ymls');

            $file = Storage::disk('public')->path('ymls/' .  $this->passedJob->getFilename());

            $settings = (new Settings())
                ->setOutputFile($file)
                ->setEncoding('UTF-8')
            ;

// Creating ShopInfo object (https://yandex.ru/support/webmaster/goods-prices/technical-requirements.xml#shop)
            $shopInfo = (new ShopInfo())
                ->setName('BestShop')
                ->setCompany('Best online seller Inc.')
                ->setUrl('http://www.best.seller.com/')
            ;


            $currencies = [];
            $categories = [];
            $offers = [];
            $deliveries = [];

            $this->products->each(
                function (array $item) {
                    $this->passedJob->setStatus(Job::STATUS_IN_PROGRESS)
                        ->save();
//                ...  here is processing of current record
                }
            );

            (new Generator($settings))->generate(
                $shopInfo,
                $currencies,
                $categories,
                $offers,
                $deliveries
            );

            $this->passedJob->setStatus(Job::STATUS_SUCCESS)
                ->save();
        } catch (Exception $exception) {
            $this->passedJob->setError($exception->getMessage())
                ->setStatus(Job::STATUS_FAILED)
                ->save();
        }
    }
}
