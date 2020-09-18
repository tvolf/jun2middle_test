<?php

namespace App\Jobs;

use App\Models\Job;
use Bukashk0zzz\YmlGenerator\Generator;
use Bukashk0zzz\YmlGenerator\Model\Category;
use Bukashk0zzz\YmlGenerator\Model\Currency;
use Bukashk0zzz\YmlGenerator\Model\Offer\OfferSimple;
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

    const YML_FOLDER = 'ymls';

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
            Storage::disk('public')->makeDirectory(self::YML_FOLDER);

            $file = Storage::disk('public')->path(self::YML_FOLDER . '/' .  $this->passedJob->getFilename());

            $settings = (new Settings())
                ->setOutputFile($file)
                ->setEncoding('UTF-8')
            ;

            $shopInfo = (new ShopInfo())
                ->setName('BestShop')
                ->setCompany('Best online seller Inc.')
                ->setUrl('http://www.best.seller.com/')
            ;

            $categories = $this->products->pluck('category')->unique()->map(function ($category, $index) {
                return [
                    'name' => $category,
                    'index' => $index + 1
                ];
            });

            $currencies = [(new Currency())->setId('USD')->setRate(1)];

            $offers = $this->products->map(function ($item, $index) use ($categories) {

                $category = $categories->first(function ($categoryItem) use ($item) {
                    return $categoryItem['name'] === $item['category'];
                });

                if (!$category) {
                    throw new Exception('Cannot find category ' . $item['category']);
                }

                $categoryIndex = $category['index'];

                return (new OfferSimple())
                    ->setId($index + 1)
                    ->setAvailable(true)
                    ->setUrl($item['url'])
                    ->setPrice($item['price'])
                    ->setCurrencyId('USD')
                    ->setCategoryId($categoryIndex)
                    ->setDelivery(false)
                    ->setName($item['name'])
                ;
            });

            $categories = $categories->map(function ($item) {
                return (new Category())->setId($item['index'])->setName($item['name']);
            });
            $deliveries = [];

            (new Generator($settings))->generate(
                $shopInfo,
                $currencies,
                $categories->toArray(),
                $offers->toArray(),
                $deliveries
            );

            $this->passedJob->setStatus(Job::STATUS_SUCCESS)->save();
        } catch (Exception $exception) {
            $this->passedJob->setError($exception->getMessage())
                ->setStatus(Job::STATUS_FAILED)
                ->save();
        }
    }
}
