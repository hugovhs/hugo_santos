<?php

namespace App\Livewire\Dashboard;

use App\Models\Producto;

use Livewire\Component;
use Livewire\Attributes\Title;
use Livewire\Attributes\Layout;
use Livewire\WithPagination;

class Productos extends Component
{
    use WithPagination;

    public string $search = '';

    public ?int $productoDetalleId = null;

    public function verDetalle(int $id): void
    {
        $this->productoDetalleId = $id;
    }

    public function cerrarDetalle(): void
    {
        $this->productoDetalleId = null;
    }

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    #[Title('Productos')]
    #[Layout('components.layouts.admin')]
    public function render()
    {
        $productos = Producto::query()
            ->when(trim($this->search) !== '', function ($query) {
                $term = '%' . trim($this->search) . '%';

                $query->where(function ($q) use ($term) {
                    $q->where('nombre', 'like', $term)
                        ->orWhere('sku', 'like', $term);
                });
            })
            ->orderBy('id', 'desc')
            ->paginate(10)
            ->withQueryString();

        return view('livewire.dashboard.productos', [
            'productos' => $productos,
            'productoDetalle' => $this->productoDetalleId
                ? Producto::query()->find($this->productoDetalleId)
                : null,
        ]);
    }
}
