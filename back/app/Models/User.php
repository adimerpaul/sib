<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'rni',
        'avatar',
        'libro',
        'categoria',
        'renovacion',
        'tipoCodumento',
        'emitido',
        'genero',
        'fechaNac',
        'paginas',
        'nivel',
        'fechaInscripcion',
        'fechaValido',
        'ci',
        'ciAnverso',
        'ciReverso',
        'ciudad',
        'departamento',
        'municipio',
        'barrio',
        'direccion',
        'telefono',
        'celular',
        'web',
        'skype',
        'facebook',
        'lat',
        'lng',
        'email',
        'email_verified_at',
        'password',
        'type'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];
}
