<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
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

    public function scopeBuscar(Builder $query, string $busqueda): Builder
    {
        $busqueda = trim($busqueda);

        if ($busqueda === '') {
            return $query;
        }

        $term = '%' . $busqueda . '%';

        return $query->where(function (Builder $query) use ($term) {
            $query->where('nombre', 'like', $term)
                ->orWhere('sku', 'like', $term);
        });
    }
}
