<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Mail extends Model
{
    use HasFactory;
    protected $fillable = [
        'fecha',
        'cite',
        'remite',
        'referencia',
        'dirigido',
        'observaciones',
        'archivoDigital',
        'type',
        'user_id',
    ];
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
