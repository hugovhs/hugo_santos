<?php

namespace App\Livewire\Home;

use App\Models\Categoria;
use App\Models\Producto;
use App\Services\CarritoService;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithoutUrlPagination;
use Livewire\WithPagination;

class ListaProductos extends Component
{
    use WithPagination, WithoutUrlPagination;

    public string $busqueda = '';

    public array $categoriasSeleccionadas = [];

    public ?float $precioMaximo = null;

    public string $disponibilidad = 'todos';

    public function mount(?string $busqueda = null, ?int $categoria = null): void
    {
        $busqueda ??= request()->query('q');
        $categoria ??= filter_var(request()->query('categoria'), FILTER_VALIDATE_INT, [
            'options' => ['min_range' => 1, 'default' => null],
        ]);

        $this->busqueda = is_string($busqueda) ? trim($busqueda) : '';
        $this->categoriasSeleccionadas = $categoria === null ? [] : [(string) $categoria];
        $this->precioMaximo = $this->precioMaximoCatalogo();
    }

    public function updatedBusqueda(): void
    {
        $this->resetPage();
    }

    public function updatedCategoriasSeleccionadas(): void
    {
        $this->resetPage();
    }

    public function updatedPrecioMaximo(): void
    {
        $this->resetPage();
    }

    public function updatedDisponibilidad(): void
    {
        $this->resetPage();
    }

    public function limpiarFiltros(): void
    {
        $this->reset(['busqueda', 'categoriasSeleccionadas', 'disponibilidad']);
        $this->precioMaximo = $this->precioMaximoCatalogo();
        $this->resetPage();
    }

    public function agregarAlCarrito(int $productoId): void
    {
        $producto = Producto::query()->where('activo', true)->find($productoId);

        if ($producto === null || (int) $producto->stock <= 0) {
            return;
        }

        $total = CarritoService::agregar($producto, 1);

        $this->dispatch('carrito-actualizado', total: $total);
    }

    protected function precioMinimoCatalogo(): float
    {
        return (float) Producto::query()->where('activo', true)->min('precio_mxn');
    }

    protected function precioMaximoCatalogo(): ?float
    {
        $maximo = Producto::query()->where('activo', true)->max('precio_mxn');

        return $maximo === null ? null : (float) $maximo + 1;
    }

    #[Layout('components.layouts.home')]
    #[Title('Productos')]
    public function render()
    {
        $categorias = Categoria::query()->orderBy('nombre')->get(['id', 'nombre']);
        $categoriasSeleccionadas = array_filter(
            $this->categoriasSeleccionadas,
            fn ($id) => filter_var($id, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]) !== false,
        );

        $precioMinimoCatalogo = $this->precioMinimoCatalogo();
        $precioMaximoCatalogo = $this->precioMaximoCatalogo();

        if ($precioMaximoCatalogo !== null && $this->precioMaximo === null) {
            $this->precioMaximo = $precioMaximoCatalogo;
        }

        if ($precioMaximoCatalogo !== null && $this->precioMaximo !== null) {
            $this->precioMaximo = max($precioMinimoCatalogo, min($this->precioMaximo, $precioMaximoCatalogo));
        }

        $disponibilidad = in_array($this->disponibilidad, ['todos', 'con', 'sin'], true)
            ? $this->disponibilidad
            : 'todos';

        $productos = Producto::query()
            ->where('activo', true)
            ->buscar($this->busqueda)
            ->when($categoriasSeleccionadas !== [], function (Builder $query) use ($categoriasSeleccionadas) {
                $query->whereHas('categorias', fn (Builder $query) => $query->whereKey($categoriasSeleccionadas));
            })
            ->when($this->precioMaximo !== null, function (Builder $query) {
                $query->where('precio_mxn', '<=', $this->precioMaximo);
            })
            ->when($disponibilidad === 'con', fn (Builder $query) => $query->where('stock', '>=', 1))
            ->when($disponibilidad === 'sin', fn (Builder $query) => $query->where('stock', '<=', 0))
            ->orderBy('nombre')
            ->orderBy('id')
            ->paginate(15, ['id', 'nombre', 'sku', 'imagen', 'precio_mxn', 'precio_usd', 'stock']);

        return view('livewire.home.lista-productos', compact(
            'categorias',
            'productos',
            'precioMinimoCatalogo',
            'precioMaximoCatalogo',
        ));
    }
}
