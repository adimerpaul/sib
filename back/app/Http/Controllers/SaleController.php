<?php

namespace App\Http\Controllers;

use App\Models\Sale;
use App\Http\Requests\StoreSaleRequest;
use App\Http\Requests\UpdateSaleRequest;
use Illuminate\Http\Request;

class SaleController extends Controller{
    public function index(){}
    public function store(StoreSaleRequest $request){
//        $request['date'] = date('Y-m-d');
//        $request['time'] = date('H:i:s');
        $request['user_id'] = $request->user()->id;
        return Sale::create($request->all());
    }
    public function getSales(Request $request){
        return Sale::whereDate('date', '>=', $request->dateIni)
            ->whereDate('date', '<=', $request->dateEnd)
            ->orderBy('id', 'desc')
            ->with('user')
            ->get();
    }
    public function anularSale(Request $request){
        $sale = Sale::find($request->id);
        $sale->status = 'Anulado';
        $sale->save();
        return $sale;
    }
    public function show(Sale $sale){ return $sale;}
    public function update(UpdateSaleRequest $request, Sale $sale){return $sale->update($request->all());}
    public function destroy(Sale $sale){return $sale->delete();}
}
