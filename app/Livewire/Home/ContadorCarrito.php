<?php

namespace App\Livewire\Home;

use App\Models\Producto;
use App\Services\CarritoService;
use Livewire\Attributes\On;
use Livewire\Component;

class ContadorCarrito extends Component
{
    public int $total = 0;

    public function mount(): void
    {
        $this->total = CarritoService::totalUnidades();
    }

    #[On('carrito-actualizado')]
    public function actualizar(?int $total = null): void
    {
        $this->total = $total ?? CarritoService::totalUnidades();
    }

    public function quitarDelCarrito(int $productoId): void
    {
        $total = CarritoService::quitar($productoId);
        $this->total = $total;

        $this->dispatch('carrito-actualizado', total: $total);
        $this->dispatch('carrito-mensaje', mensaje: 'Producto eliminado del carrito');
    }

    public function render()
    {
        $carrito = CarritoService::obtener();

        $productos = $carrito === []
            ? collect()
            : Producto::query()
                ->whereKey(array_keys($carrito))
                ->get(['id', 'nombre', 'sku', 'imagen', 'precio_mxn', 'stock']);

        $items = $productos
            ->map(fn (Producto $producto) => [
                'producto' => $producto,
                'cantidad' => $carrito[$producto->id] ?? 0,
            ])
            ->filter(fn (array $item) => $item['cantidad'] > 0)
            ->values();

        $subtotal = round($items->sum(
            fn (array $item) => (float) $item['producto']->precio_mxn * $item['cantidad'],
        ), 2);

        return view('livewire.home.contador-carrito', compact('items', 'subtotal'));
    }
}
