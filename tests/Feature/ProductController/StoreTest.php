<?php

namespace Tests\Feature\ProductController;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
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
                'name' => 'Product mame',
                'category' => 'Product category',
                'price' => '1.22',
                'image' => 'http://image.com/some_image.jpg'
            ]
        ];

        $this->postJson($this->route, ['items' => $items])
            ->assertStatus(200)
            ->assertJsonStructure(['message', 'job'])
            ->assertJsonFragment(['message' => __('product.yml_generation_started_successfully')]);
    }
}
