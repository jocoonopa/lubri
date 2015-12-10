<?php

namespace App\Http\Controllers\Pos\Store;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Model\Pos\Store\Store;
use App\Model\Pos\Store\StoreArea;

class StoreController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $stores = Store::all();

        return view('pos.store.store.index', compact('stores'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $areas = StoreArea::lists('name', 'id');
 
        return view('pos.store.store.create', compact('areas'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $store = Store::create($request->all());

        $this->associateForeignKey($store);

        return redirect('pos/store/store');
    }

    /**
     * Display the specified resource.
     *
     * @param  Store $store
     * @return \Illuminate\Http\Response
     */
    public function show(Store $store)
    {
        $areas = StoreArea::lists('name', 'id');

        return view('pos.store.store.edit', compact('store', 'areas'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  Store $store
     * @return \Illuminate\Http\Response
     */
    public function edit(Store $store)
    {
        $areas = StoreArea::lists('name', 'id');

        return view('pos.store.store.edit', compact('store', 'areas'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  Store $store
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Store $store)
    {
        $store->update($request->all());
        $this->associateForeignKey($store);

        return redirect('pos/store/store');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  Store $store
     * @return \Illuminate\Http\Response
     */
    public function destroy(Store $store)
    {
        return $store;
    }

    /**
     * associateForeignKey 
     * 
     * @param  Store $store 
     * @return Store $store     
     */
    protected function associateForeignKey(Store $store)
    {
        $store->storeArea()->associate(StoreArea::find($request->input('store_area')));

        return $store->save();
    }
}
