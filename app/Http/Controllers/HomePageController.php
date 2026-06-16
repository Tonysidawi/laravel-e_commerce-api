<?php

namespace App\Http\Controllers;

use App\Http\Requests\Home\HomePageIndexRequest;
use App\Http\Resources\Product\ProductResource;

class HomePageController extends Controller
{
    public function index(HomePageIndexRequest $request)
    {
        return $this->success(ProductResource::collection($request->getProducts()));
    }
}
