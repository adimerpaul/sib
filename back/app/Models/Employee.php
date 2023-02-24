<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    use HasFactory;
    protected $fillable = [
        'ci', 
    'expedido', 
    'nombre', 
    'apellido', 
    'fechanac', 
    'sexo',
    'fechaing',
    'charge_id'
    ];

    public function charge(){
        return $this->belongsTo(\App\Models\Charge::class);
    }
}
