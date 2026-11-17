<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Producto extends Model
{
    use SoftDeletes;
    protected $table = 'productos';
    protected $fillable = [
        'sku',
        'nombre',
        'descripcion_corta',
        'descripcion_larga',
        'precio_usd',
        'precio_mxn',
        'imagen',
        'stock',
        'fecha_vigencia',
        'activo'
    ];
}
