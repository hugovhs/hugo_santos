<?php

namespace App\Livewire\Dashboard;

use App\Models\Pedido;
use Carbon\Carbon;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

class Pedidos extends Component
{
    use WithPagination;

    #[Url]
    public string $fechaInicio = '';

    #[Url]
    public string $fechaFin = '';

    public ?int $pedidoDetalleId = null;

    public function verDetalle(int $id): void
    {
        $this->pedidoDetalleId = $id;
    }

    public function cerrarDetalle(): void
    {
        $this->pedidoDetalleId = null;
    }

    public function updatedFechaInicio(): void
    {
        $this->resetPage();
    }

    public function updatedFechaFin(): void
    {
        $this->resetPage();
    }

    public function limpiarFiltros(): void
    {
        $this->reset(['fechaInicio', 'fechaFin']);
        $this->resetPage();
    }

    public function getFiltrosActivosProperty(): bool
    {
        return trim($this->fechaInicio) !== '' || trim($this->fechaFin) !== '';
    }

    protected function fechaValida(string $valor): ?string
    {
        $valor = trim($valor);

        if ($valor === '') {
            return null;
        }

        try {
            return Carbon::createFromFormat('Y-m-d', $valor)->toDateString();
        } catch (\Throwable) {
            return null;
        }
    }

    #[Title('Pedidos')]
    #[Layout('components.layouts.admin')]
    public function render()
    {
        $inicio = $this->fechaValida($this->fechaInicio);
        $fin = $this->fechaValida($this->fechaFin);

        $rangoInvertido = $inicio !== null && $fin !== null && $inicio > $fin;

        $pedidos = Pedido::query()
            ->when($inicio !== null, fn ($query) => $query->whereDate('fecha', '>=', $inicio))
            ->when($fin !== null, fn ($query) => $query->whereDate('fecha', '<=', $fin))
            ->orderByDesc('fecha')
            ->orderByDesc('id')
            ->paginate(10)
            ->withQueryString();

        return view('livewire.dashboard.pedidos', [
            'pedidos' => $pedidos,
            'rangoInvertido' => $rangoInvertido,
            'pedidoDetalle' => $this->pedidoDetalleId
                ? Pedido::query()->with(['detalles.producto'])->find($this->pedidoDetalleId)
                : null,
        ]);
    }
}
