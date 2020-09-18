<?php

namespace App\Http\Controllers;

use App\Http\Requests\UploadProductsRequest;
use App\Jobs\GenerateYmlJob;
use App\Models\Job;
use Illuminate\Support\Str;

class ProductController extends Controller
{
    public function store(UploadProductsRequest $request)
    {
        $products = $request->input('items');

        $fileName = Str::random(20) . '.yml';

        $job = Job::query()->create(
            [
                'filename' => $fileName,
                'status' => Job::STATUS_NEW
            ]
        );

        GenerateYmlJob::dispatch($job, collect($products));

        return response()->json(['message' => __('product.yml_generation_started_successfully')]);
    }
}
