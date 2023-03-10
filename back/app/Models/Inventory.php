<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Inventory extends Model
{
    use HasFactory;
    protected $fillable= [
        'name',
        'code',
        'description',
        'price',
        'image',
        'quantity',
        'category_id',
        'user_id',
    ];
    public function category(){
        return $this->belongsTo('App\Models\Category');
    }
    public function user(){
        return $this->belongsTo('App\Models\User');
    }
}
