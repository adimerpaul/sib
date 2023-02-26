<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Letter extends Model
{
    use HasFactory;
    protected $fillable= [    'name',
    'status',
    'description',
    'date',
    'user_id',
    ];
}
