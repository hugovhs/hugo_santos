<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Direccion;

class Cliente extends Model
{
    protected $table = 'clientes';
    protected $fillable = [
        'nombre',
        'correo',
        'tipo',
        'password'
    ];

    public function direcciones()
    {
        return $this->hasMany(Direccion::class, 'cliente_id');
    }
}
