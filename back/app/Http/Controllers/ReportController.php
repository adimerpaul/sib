<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Sale;

class ReportController extends Controller
{
    //
    public function reporteIG(Request $request){
        return Sale::with('user')->whereDate('date','>=',$request->ini)->whereDate('date','<=',$request->fin)->where('type',$request->tipo)->get();
    }
}
