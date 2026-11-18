<?php

namespace App\Livewire\Home;

use App\Models\Producto;
use App\Services\CarritoService;
use Livewire\Attributes\Layout;
use Livewire\Component;

class DetalleProducto extends Component
{
    public Producto $producto;

    public int $cantidad = 1;

    public function mount(Producto $producto): void
    {
        abort_if(! $producto->activo, 404);

        $this->producto = $producto;
        $this->cantidad = $producto->stock > 0 ? 1 : 0;
    }

    public function updatedCantidad(mixed $value): void
    {
        $this->cantidad = $this->normalizarCantidad($value);
    }

    public function incrementar(): void
    {
        $this->cantidad = $this->normalizarCantidad($this->cantidad + 1);
    }

    public function decrementar(): void
    {
        $this->cantidad = $this->normalizarCantidad($this->cantidad - 1);
    }

    public function agregarAlCarrito(): void
    {
        $this->producto->refresh();
        $this->cantidad = $this->normalizarCantidad($this->cantidad);

        if ($this->cantidad < 1) {
            return;
        }

        $total = CarritoService::agregar($this->producto, $this->cantidad);

        $this->dispatch('carrito-actualizado', total: $total);
        $this->dispatch('carrito-mensaje', mensaje: 'Producto añadido al carrito');
    }

    protected function normalizarCantidad(mixed $value): int
    {
        $stock = (int) $this->producto->stock;

        if ($stock <= 0) {
            return 0;
        }

        $cantidad = filter_var($value, FILTER_VALIDATE_INT);

        if ($cantidad === false) {
            return 1;
        }

        return max(1, min($cantidad, $stock));
    }

    #[Layout('components.layouts.home')]
    public function render()
    {
        return view('livewire.home.detalle-producto')
            ->title($this->producto->nombre ?: 'Detalle del producto');
    }
}
