<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    use HasFactory;
    protected $fillable = [
    'fecha',
    'horario',
    'total',
    'trabajo',
    'employee_id',
];
public function employee(){
    return $this->belongsTo(\App\Models\Employee::class);
}
}
