<?php

namespace App\Livewire\Home;

use App\Models\Producto;
use App\Services\CarritoService;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use Livewire\Attributes\Title;
use Livewire\Component;

class Carrito extends Component
{
    private const IVA_TASA = 0.16;

    public array $cantidades = [];

    public function mount(): void
    {
        $this->sincronizar();
    }

    /**
     * Recarga las cantidades desde sesión, depurando productos
     */
    protected function sincronizar(): void
    {
        $carrito = CarritoService::obtener();

        foreach ($carrito as $id => $cantidad) {
            $producto = Producto::query()->find($id);

            if ($producto === null || ! $producto->activo || (int) $producto->stock <= 0) {
                CarritoService::quitar($id);
                unset($carrito[$id]);

                continue;
            }

            $normalizada = max(1, min($cantidad, (int) $producto->stock));

            if ($normalizada !== $cantidad) {
                CarritoService::actualizar($id, $normalizada);
            }

            $carrito[$id] = $normalizada;
        }

        $this->cantidades = $carrito;
    }

    protected function notificar(): void
    {
        $this->cantidades = CarritoService::obtener();
        $this->dispatch('carrito-actualizado', total: array_sum($this->cantidades));
    }

    /**
     * Se sincroniza si otro componente modificó el carrito.
     */
    #[On('carrito-actualizado')]
    public function refrescarDesdeEvento(?int $total = null): void
    {
        if ($total !== null && $total === array_sum($this->cantidades)) {
            return;
        }

        $this->sincronizar();
    }

    public function incrementar(int $productoId): void
    {
        CarritoService::actualizar($productoId, ($this->cantidades[$productoId] ?? 0) + 1);
        $this->notificar();
    }

    public function decrementar(int $productoId): void
    {
        CarritoService::actualizar($productoId, ($this->cantidades[$productoId] ?? 0) - 1);
        $this->notificar();
    }

    public function updatedCantidades(mixed $value, string $key): void
    {
        $cantidad = filter_var($value, FILTER_VALIDATE_INT);

        CarritoService::actualizar((int) $key, $cantidad === false ? 1 : $cantidad);
        $this->notificar();
    }

    public function quitarDelCarrito(int $productoId): void
    {
        $total = CarritoService::quitar($productoId);
        $this->cantidades = CarritoService::obtener();

        $this->dispatch('carrito-actualizado', total: $total);
        $this->dispatch('carrito-mensaje', mensaje: 'Producto eliminado del carrito');
    }

    public function limpiar(): void
    {
        CarritoService::limpiar();
        $this->cantidades = [];

        $this->dispatch('carrito-actualizado', total: 0);
        $this->dispatch('carrito-mensaje', mensaje: 'Carrito vaciado');
    }

    #[Layout('components.layouts.home')]
    #[Title('Carrito')]
    public function render()
    {
        $productos = $this->cantidades === []
            ? collect()
            : Producto::query()
                ->whereKey(array_keys($this->cantidades))
                ->get(['id', 'nombre', 'sku', 'imagen', 'precio_mxn', 'stock']);

        $items = $productos
            ->map(fn (Producto $producto) => [
                'producto' => $producto,
                'cantidad' => $this->cantidades[$producto->id] ?? 0,
                'linea' => round((float) $producto->precio_mxn * ($this->cantidades[$producto->id] ?? 0), 2),
            ])
            ->filter(fn (array $item) => $item['cantidad'] > 0)
            ->values();

        $subtotal = round($items->sum(fn (array $item) => $item['linea']), 2);
        $iva = round($subtotal * self::IVA_TASA, 2);
        $total = round($subtotal + $iva, 2);

        return view('livewire.home.carrito', compact('items', 'subtotal', 'iva', 'total'));
    }
}
