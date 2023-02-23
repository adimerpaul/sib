<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sale extends Model
{
    use HasFactory;
    protected $fillable = [
        'name',
        'amount',
        'date',
        'time',
        'status',
        'type',
        'description',
        'user_id',
    ];
    public function user(){
        return $this->belongsTo(\App\Models\User::class);
    }
}
