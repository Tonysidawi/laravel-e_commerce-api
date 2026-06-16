<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProductCategory\ProductCategoryIndexRequest;
use App\Http\Resources\Product\ProductResource;
use App\Models\ProductCategory;

class ProductCategoryController extends Controller
{
    /**
     * Fetching products for a category.
     */
    public function index(ProductCategoryIndexRequest $request, ProductCategory $productCategory)
    {
        return ProductResource::collection($request->getProductForACategory($productCategory));
    }
}
