<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payroll extends Model
{
    use HasFactory;
    protected $fillable = [
    'fecha',
    'mes',
    'anio',
    'salario',
    'bonificacion',
    'descuento',
    'days',
    'total',
    'employee_id',
    ];
    public function employee(){
        return $this->belongsTo(\App\Models\Employee::class);
    }
}
