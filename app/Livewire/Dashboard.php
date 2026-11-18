<?php

namespace App\Livewire;

use App\Models\Pedido;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

class Dashboard extends Component
{
    #[Title('Dashboard')]
    #[Layout('components.layouts.admin')]
    public function render()
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        // Últimas 10 órdenes generadas (sin detalle: folio, total, correo).
        $ultimasOrdenes = Pedido::query()
            ->orderByDesc('fecha')
            ->orderByDesc('id')
            ->limit(10)
            ->get(['id', 'folio', 'total', 'correo', 'fecha', 'estatus']);

        // Top 3 productos más vendidos (por unidades).
        $topProductos = DB::table('pedido_detalle as pd')
            ->join('productos as p', 'p.id', '=', 'pd.producto_id')
            ->select(
                'p.id',
                'p.nombre',
                'p.sku',
                'p.imagen',
                DB::raw('SUM(pd.cantidad) as unidades'),
                DB::raw('SUM(pd.cantidad * pd.precio_mxn) as ingresos')
            )
            ->groupBy('p.id', 'p.nombre', 'p.sku', 'p.imagen')
            ->orderByDesc('unidades')
            ->limit(3)
            ->get();

        // Ventas de los últimos 7 días (incluye hoy).
        $dias = collect(range(6, 0))->map(fn (int $i) => Carbon::today()->subDays($i));
        $etiquetas = $dias->map(fn (Carbon $d) => $d->translatedFormat('D d M'))->values();

        $ventasPorDia = Pedido::query()
            ->where('fecha', '>=', Carbon::today()->subDays(6)->startOfDay())
            ->select(DB::raw('DATE(fecha) as dia'), DB::raw('SUM(total) as total'))
            ->groupBy(DB::raw('DATE(fecha)'))
            ->pluck('total', 'dia');

        $ventas = $dias->map(fn (Carbon $d) => (float) ($ventasPorDia[$d->toDateString()] ?? 0))->values();

        return view('livewire.dashboard', [
            'ultimasOrdenes' => $ultimasOrdenes,
            'topProductos' => $topProductos,
            'ventasEtiquetas' => $etiquetas,
            'ventasMontos' => $ventas,
            'ventasTotal' => $ventas->sum(),
        ]);
    }
}
