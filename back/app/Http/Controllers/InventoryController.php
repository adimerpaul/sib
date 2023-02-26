<?php

namespace App\Http\Controllers;

use App\Models\Inventory;
use App\Http\Requests\StoreInventoryRequest;
use App\Http\Requests\UpdateInventoryRequest;

class InventoryController extends Controller{
    public function index(){
        return Inventory::with('category')->with('user')->get();
    }
    public function show(Inventory $inventory){ return $inventory;}
    public function store(StoreInventoryRequest $request){
        if ($request->image=='' || $request->image==null) {
            $request->merge(['image'=>'default.png']);
        }
        return Inventory::create($request->all());
    }
    public function update(UpdateInventoryRequest $request, Inventory $inventory){ $inventory->update($request->all()); return $inventory;}
    public function destroy(Inventory $inventory){ $inventory->delete(); return $inventory;}
}
