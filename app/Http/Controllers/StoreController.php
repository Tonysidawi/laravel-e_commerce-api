<?php

namespace App\Http\Controllers;

use App\Http\Requests\Store\StoreIndexRequest;
use App\Http\Requests\Store\StoreRequest;
use App\Http\Requests\Store\StoreUpdateResquest;
use App\Http\Resources\Store\StoreResource;
use App\Models\Store;

class StoreController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(StoreIndexRequest $request)
    {
        return StoreResource::collection(auth()->user()->stores()
            ->with('images', 'mainImage')
            ->latest()
            ->paginate(20));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreRequest $request)
    {
        $store = Store::createStore($request->validated());

        return $this->success(new StoreResource($store->loadStoreRelations()), 'Store created successfully');
    }

    /**
     * Display the specified resource.
     */
    public function show(Store $store)
    {
        return $this->success(new StoreResource($store->loadStoreRelations()));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(StoreUpdateResquest $request, Store $store)
    {
        $store = Store::updateStore($store, $request->validated());

        return $this->success(new StoreResource($store->loadStoreRelations()), 'Store updated successfully');
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
