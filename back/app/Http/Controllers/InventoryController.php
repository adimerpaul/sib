<?php

namespace App\Http\Controllers;

use App\Models\Inventory;
use App\Http\Requests\StoreInventoryRequest;
use App\Http\Requests\UpdateInventoryRequest;

class InventoryController extends Controller{
    public function index(){ return Inventory::all();}
    public function show(Inventory $inventory){ return $inventory;}
    public function store(StoreInventoryRequest $request){ return Inventory::create($request->all());}
    public function update(UpdateInventoryRequest $request, Inventory $inventory){ $inventory->update($request->all()); return $inventory;}
    public function destroy(Inventory $inventory){ $inventory->delete(); return $inventory;}
}
