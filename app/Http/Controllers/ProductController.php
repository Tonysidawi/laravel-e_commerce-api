<?php

namespace App\Http\Controllers;

use App\Http\Requests\Product\ProductIndexRequest;
use App\Http\Requests\Product\ProductRequest;
use App\Http\Requests\Product\ProductUpdateRequest;
use App\Http\Resources\Product\ProductResource;
use App\Models\Product;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(ProductIndexRequest $request)
    {
        return ProductResource::collection(Product::query()
            ->with('store', 'productCategory', 'images')
            ->orderByDesc('created_at')
            ->paginate($request->input('per_page', 10)));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(ProductRequest $request)
    {
        $product = Product::createProduct($request->validated());

        return $this->success(new ProductResource($product->loadProductRelations()), 'Product created successfully');
    }

    /**
     * Display the specified resource.
     */
    public function show(Product $product)
    {
        return $this->success(new ProductResource($product->loadProductRelations()));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(ProductUpdateRequest $request, Product $product)
    {
        $product = Product::updateProduct($product, $request->validated());

        return $this->success(new ProductResource($product->loadProductRelations()), 'Product updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Product $product)
    {
        $product->delete();

        return $this->success([], 'Product deleted successfully');
    }
}
