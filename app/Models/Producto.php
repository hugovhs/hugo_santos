<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

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

    public function categorias(): BelongsToMany
    {
        return $this->belongsToMany(
            Categoria::class,
            'producto_categoria',
            'producto_id',
            'categoria_id'
        );
    }

    public function categoria(): ?Categoria
    {
        return $this->categorias()->first();
    }
}
