<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PedidoDetalle extends Model
{
    protected $table = 'pedido_detalle';
    protected $fillable = [
        'pedido_id',
        'producto_id',
        'cantidad',
        'precio_usd',
        'precio_mxn'
    ];
    public $timestamps = false;

    public function producto()
    {
        return $this->belongsTo(Producto::class, 'producto_id');
    }

    public function pedido()
    {
        return $this->belongsTo(Pedido::class, 'pedido_id');
    }
}
