<?php

namespace Tests\Feature\Controllers;

use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\Store;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductCategoryTest extends TestCase
{
    use RefreshDatabase;

    /**
     * GET 'product-categories/{productCategory}/products'
     */
    public function test_fetching_products_for_a_category(): void
    {
        $store = Store::factory()->create();

        $productCategory = ProductCategory::factory()->create();

        Product::factory()->count(3)->create([
            'store_id' => $store->id,
            'product_category_id' => $productCategory->id,
        ]);

        $products = Product::query()
            ->where('product_category_id', $productCategory->id)
            ->orderByDesc('created_at')
            ->get();

        $this->getJson("/api/product-categories/{$productCategory->id}/products")
            ->assertSuccessful()
            ->assertJsonCount(3, 'data')
            ->assertJsonFragment([
                'id' => $products[0]->id,
                'name' => $products[0]->name,
            ]);
    }
}
