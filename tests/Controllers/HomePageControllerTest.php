<?php

use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\Store;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class HomePageControllerTest extends TestCase
{
    use RefreshDatabase;

    /**
     * GET 'home-page'
     */
    public function test_user_can_get_home_page_products()
    {
        $stores = Store::factory()->count(5)->create();

        $productCategories = ProductCategory::factory()->count(5)->create();

        $products = Product::factory()->count(20)->create([
            'store_id' => $stores->random()->id,
            'product_category_id' => $productCategories->random()->id,
        ]);

        $response = $this->getJson('/api/home-page');

        $response->assertSuccessful()
            ->assertJsonFragment([
                'id' => $products->first()->id,
                'name' => $products->first()->name,
            ]);

    }
}
