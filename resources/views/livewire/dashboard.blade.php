<div class="space-y-6">
    <div class="flex flex-wrap items-end justify-between gap-3">
        <div>
            <h1 class="text-2xl font-bold text-slate-900">Dashboard</h1>
            <p class="mt-1 text-sm text-slate-500">Resumen de ventas de los últimos 7 días.</p>
        </div>
        <p class="rounded-full bg-white px-4 py-2 text-sm font-semibold text-slate-700 shadow-sm ring-1 ring-slate-200">
            Ventas del periodo: <span class="text-indigo-600">${{ number_format($ventasTotal, 2) }} MXN</span>
        </p>
    </div>

    {{-- Gráfica de ventas últimos 7 días --}}
    <section class="rounded-2xl bg-white p-5 shadow-sm ring-1 ring-slate-200">
        <div class="mb-4 flex items-center justify-between gap-3">
            <div>
                <h2 class="text-base font-semibold text-slate-900">Ventas de los últimos 7 días</h2>
                <p class="text-sm text-slate-500">Monto total por día (MXN).</p>
            </div>
            <span class="rounded-full bg-indigo-50 px-3 py-1 text-xs font-semibold text-indigo-700">Últimos 7 días</span>
        </div>

        <div class="relative h-72" wire:ignore>
            <canvas id="grafica-ventas"></canvas>
        </div>
    </section>

    <div class="grid gap-6 lg:grid-cols-5">
        {{-- Últimas 10 órdenes --}}
        <section class="rounded-2xl bg-white p-5 shadow-sm ring-1 ring-slate-200 lg:col-span-3">
            <div class="mb-4 flex items-center justify-between gap-3">
                <div>
                    <h2 class="text-base font-semibold text-slate-900">Últimas órdenes generadas</h2>
                    <p class="text-sm text-slate-500">Las 10 más recientes.</p>
                </div>
                <i class="fa-solid fa-receipt text-lg text-slate-300" aria-hidden="true"></i>
            </div>

            @if ($ultimasOrdenes->isEmpty())
                <p class="rounded-xl bg-slate-50 px-4 py-8 text-center text-sm text-slate-500">Aún no hay órdenes generadas.</p>
            @else
                <div class="overflow-x-auto">
                    <table class="w-full min-w-[480px] text-left text-sm">
                        <thead>
                            <tr class="border-b border-slate-200 text-xs uppercase tracking-wide text-slate-500">
                                <th class="py-2 pr-4 font-semibold">Folio</th>
                                <th class="py-2 pr-4 font-semibold">Comprador</th>
                                <th class="py-2 text-right font-semibold">Total</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @foreach ($ultimasOrdenes as $orden)
                                <tr class="hover:bg-slate-50">
                                    <td class="py-2.5 pr-4 font-mono text-xs font-semibold text-slate-900">{{ $orden->folio }}</td>
                                    <td class="max-w-[200px] truncate py-2.5 pr-4 text-slate-600" title="{{ $orden->correo }}">{{ $orden->correo }}</td>
                                    <td class="py-2.5 text-right font-semibold text-slate-900">${{ number_format($orden->total, 2) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </section>

        {{-- Top 3 productos más vendidos --}}
        <section class="rounded-2xl bg-white p-5 shadow-sm ring-1 ring-slate-200 lg:col-span-2">
            <div class="mb-4 flex items-center justify-between gap-3">
                <div>
                    <h2 class="text-base font-semibold text-slate-900">Top 3 productos más vendidos</h2>
                    <p class="text-sm text-slate-500">Por unidades vendidas.</p>
                </div>
                <i class="fa-solid fa-trophy text-lg text-amber-400" aria-hidden="true"></i>
            </div>

            @if ($topProductos->isEmpty())
                <p class="rounded-xl bg-slate-50 px-4 py-8 text-center text-sm text-slate-500">Sin ventas registradas.</p>
            @else
                <ol class="space-y-3">
                    @foreach ($topProductos as $i => $producto)
                        <li class="flex items-center gap-3 rounded-xl border border-slate-200 p-3">
                            <span
                                class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full text-sm font-bold
                                {{ $i === 0 ? 'bg-amber-100 text-amber-700' : ($i === 1 ? 'bg-slate-200 text-slate-700' : 'bg-orange-100 text-orange-700') }}">
                                {{ $i + 1 }}
                            </span>
                            <img src="{{ $producto->imagen ? asset('storage/' . $producto->imagen) : asset('images/products/placeholder.jpg') }}" alt="{{ $producto->nombre }}"
                                class="h-11 w-11 shrink-0 rounded-lg object-cover ring-1 ring-slate-200">
                            <div class="min-w-0 flex-1">
                                <p class="truncate text-sm font-semibold text-slate-900">{{ $producto->nombre }}</p>
                                <p class="font-mono text-xs text-slate-500">{{ $producto->sku }}</p>
                            </div>
                            <div class="shrink-0 text-right">
                                <p class="text-sm font-bold text-indigo-600">{{ number_format($producto->unidades) }} uds.</p>
                                <p class="text-xs text-slate-500">${{ number_format($producto->ingresos, 2) }}</p>
                            </div>
                        </li>
                    @endforeach
                </ol>
            @endif
        </section>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const canvas = document.getElementById('grafica-ventas');
            if (!canvas || typeof Chart === 'undefined') return;
            if (canvas.dataset.iniciado === '1') return;
            canvas.dataset.iniciado = '1';

            new Chart(canvas, {
                type: 'bar',
                data: {
                    labels: @json($ventasEtiquetas),
                    datasets: [{
                        label: 'Ventas (MXN)',
                        data: @json($ventasMontos),
                        backgroundColor: 'rgba(79, 70, 229, 0.7)',
                        borderColor: 'rgb(79, 70, 229)',
                        borderWidth: 1,
                        borderRadius: 6,
                    }],
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            callbacks: {
                                label: (ctx) => ' $' + Number(ctx.parsed.y).toLocaleString('es-MX', { minimumFractionDigits: 2 }) + ' MXN',
                            },
                        },
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                callback: (v) => '$' + Number(v).toLocaleString('es-MX', { maximumFractionDigits: 0 }),
                            },
                        },
                    },
                },
            });
        });
    </script>
</div>
