<?php

namespace App\Livewire\Home;

use App\Models\Categoria;
use App\Models\Producto;
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

    public function mount(?string $busqueda = null, ?int $categoria = null): void
    {
        $busqueda ??= request()->query('q');
        $categoria ??= filter_var(request()->query('categoria'), FILTER_VALIDATE_INT, [
            'options' => ['min_range' => 1, 'default' => null],
        ]);

        $this->busqueda = is_string($busqueda) ? trim($busqueda) : '';
        $this->categoriasSeleccionadas = $categoria === null ? [] : [(string) $categoria];
    }

    public function updatedBusqueda(): void
    {
        $this->resetPage();
    }

    public function updatedCategoriasSeleccionadas(): void
    {
        $this->resetPage();
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

        $productos = Producto::query()
            ->where('activo', true)
            ->buscar($this->busqueda)
            ->when($categoriasSeleccionadas !== [], function (Builder $query) use ($categoriasSeleccionadas) {
                $query->whereHas('categorias', fn (Builder $query) => $query->whereKey($categoriasSeleccionadas));
            })
            ->orderBy('nombre')
            ->orderBy('id')
            ->paginate(15, ['id', 'nombre', 'sku', 'imagen', 'precio_mxn', 'precio_usd']);

        return view('livewire.home.lista-productos', compact('categorias', 'productos'));
    }
}
