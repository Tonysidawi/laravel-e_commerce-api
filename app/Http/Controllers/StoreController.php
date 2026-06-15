<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\StoreIndexRequest;
use App\Http\Resources\StoreResource;
use App\Http\Requests\StoreRequest;
use App\Models\Store;
use App\Http\Requests\StoreUpdateResquest;

class StoreController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(StoreIndexRequest $request)
    {
        return StoreResource::collection($request->getStores());
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreRequest $request)
    {
        $store = Store::createStore($request->validated());

        return $this->success(new StoreResource($store), 'Store created successfully');
    }

    /**
     * Display the specified resource.
     */
    public function show(Store $store)
    {
        return $this->success(new StoreResource($store));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(StoreUpdateResquest $request, Store $store)
    {
        $store = Store::updateStore($store, $request->validated());

        return $this->success(new StoreResource($store), 'Store updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Store $store)
    {
        abort_if($store->user_id !== auth()->id(), 403);

        $store->delete();

        return $this->success([], 'Store deleted successfully');
    }
}
