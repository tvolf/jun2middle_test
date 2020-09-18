<?php

namespace Tests\Feature\ProductController;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Testing\TestResponse;
use Tests\TestCase;

class StoreTest extends TestCase
{
    use RefreshDatabase;

    protected string $route;

    public function setUp(): void
    {
        parent::setUp();

        $this->route = route('api.products.store');
    }

    /**
     * @test
     * @return void
     */
    public function check_validation(): void
    {
        $invalidData = [
            [['name' => Str::random(256)]],
            [['name' => '']],
            [['price' => 'asmnbvsdfg']],
            [['price' => '']],
            [['image' => 'asmnbvsdfg']],
            [['image' => '']],
            [['category' => Str::random(256)]],
            [['category' => '']]
        ];

        foreach ($invalidData as $items) {
            $key = array_keys($items[0])[0];

            $this->postJson($this->route, ['items' => $items])
                ->assertStatus(422)
                ->assertJsonValidationErrors('items.0.' . $key);
        }
    }

    /**
     * @test
     * @return void
     */
    public function store_products_successfully(): void
    {
        $items = [
            [
                'name' => 'Product name',
                'category' => 'Product category',
                'price' => '1.22',
                'url' => 'http://test.com/?product=123',
                'image' => 'http://image.com/some_image.jpg'
            ]
        ];

        /** @var TestResponse|JsonResponse $response */
        $response = $this->postJson($this->route, ['items' => $items])
            ->assertStatus(200)
            ->assertJsonStructure(['message', 'job'])
            ->assertJsonFragment(['message' => __('product.yml_generation_started_successfully')]);

        $responseData = $response->getData();
        $fileName = $responseData->job->filename;
        $this->filesToDelete[] = Storage::disk('public')->path('ymls/' . $fileName);
    }
}
