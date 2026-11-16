<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\PedidoDetalle;

class Pedido extends Model
{
    protected $table = 'pedidos';
    protected $fillable = [
        'folio',
        'correo',
        'subtotal',
        'impuestos',
        'total',
        'estatus',
        'fecha'
    ];
    public $timestamps = false;

    public function detalles()
    {
        return $this->hasMany(PedidoDetalle::class, 'pedido_id');
    }
}
