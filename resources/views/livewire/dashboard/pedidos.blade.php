<div>
    <div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
        <div>
            <h1 class="text-xl font-semibold text-slate-900">Pedidos</h1>
            <p class="mt-1 text-sm text-slate-500">Listado de pedidos registrados.</p>
        </div>

        {{-- Filtros por rango de fechas (tiempo real, sin recargar) --}}
        <div class="flex flex-col gap-3 sm:flex-row sm:items-end">
            <div>
                <label for="filtro-fecha-inicio" class="block text-xs font-medium text-slate-600">Desde</label>
                <input
                    id="filtro-fecha-inicio"
                    type="date"
                    wire:model.live="fechaInicio"
                    class="mt-1 rounded-md border border-slate-300 bg-white px-3 py-2 text-sm text-slate-900 shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500"
                />
            </div>

            <div>
                <label for="filtro-fecha-fin" class="block text-xs font-medium text-slate-600">Hasta</label>
                <input
                    id="filtro-fecha-fin"
                    type="date"
                    wire:model.live="fechaFin"
                    class="mt-1 rounded-md border border-slate-300 bg-white px-3 py-2 text-sm text-slate-900 shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500"
                />
            </div>

            @if ($this->filtrosActivos)
                <button
                    type="button"
                    wire:click="limpiarFiltros"
                    class="inline-flex items-center justify-center gap-2 rounded-md border border-slate-300 bg-white px-3 py-2 text-sm font-semibold text-slate-600 shadow-sm transition hover:bg-slate-50 hover:text-slate-900"
                >
                    <i class="fa-solid fa-xmark" aria-hidden="true"></i>
                    Limpiar
                </button>
            @endif
        </div>
    </div>

    @if ($rangoInvertido)
        <p class="mb-4 rounded-md border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-800">
            <i class="fa-solid fa-triangle-exclamation mr-2" aria-hidden="true"></i>
            La fecha «Desde» es posterior a la fecha «Hasta». Ajusta el rango para ver resultados.
        </p>
    @endif

    {{-- Tabla de pedidos --}}
    <div class="overflow-hidden rounded-lg border border-slate-200 bg-white shadow-sm">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200 text-sm">
                <thead class="bg-slate-50">
                    <tr>
                        <th scope="col" class="px-4 py-3 text-left font-semibold text-slate-700">Folio</th>
                        <th scope="col" class="px-4 py-3 text-left font-semibold text-slate-700">Correo</th>
                        <th scope="col" class="px-4 py-3 text-right font-semibold text-slate-700">Total</th>
                        <th scope="col" class="px-4 py-3 text-center font-semibold text-slate-700">Estatus</th>
                        <th scope="col" class="px-4 py-3 text-left font-semibold text-slate-700">Fecha</th>
                        <th scope="col" class="px-4 py-3 text-center font-semibold text-slate-700">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200">
                    @forelse ($pedidos as $pedido)
                        <tr class="hover:bg-slate-50">
                            <td class="whitespace-nowrap px-4 py-3 font-mono text-xs font-semibold text-slate-900">{{ $pedido->folio }}</td>
                            <td class="max-w-[220px] truncate px-4 py-3 text-slate-600" title="{{ $pedido->correo }}">{{ $pedido->correo }}</td>
                            <td class="whitespace-nowrap px-4 py-3 text-right font-semibold text-slate-900">${{ number_format($pedido->total, 2) }}</td>
                            <td class="whitespace-nowrap px-4 py-3 text-center">
                                <span class="inline-flex items-center rounded-full bg-slate-100 px-2.5 py-0.5 text-xs font-medium text-slate-600">{{ $pedido->estatus }}</span>
                            </td>
                            <td class="whitespace-nowrap px-4 py-3 text-slate-600">{{ \Carbon\Carbon::parse($pedido->fecha)->format('d/m/Y H:i') }}</td>
                            <td class="whitespace-nowrap px-4 py-3">
                                <div class="flex items-center justify-center gap-2">
                                    <button
                                        type="button"
                                        wire:click="verDetalle({{ $pedido->id }})"
                                        class="inline-flex items-center gap-2 rounded-md border border-slate-300 bg-white px-3 py-1.5 text-xs font-semibold text-slate-700 shadow-sm transition hover:bg-indigo-50 hover:text-indigo-700"
                                    >
                                        <i class="fa-solid fa-eye" aria-hidden="true"></i>
                                        Ver detalle
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-4 py-10 text-center text-slate-500">
                                @if ($this->filtrosActivos)
                                    No hay pedidos en el rango de fechas seleccionado.
                                @else
                                    No hay pedidos registrados.
                                @endif
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Paginación --}}
    <div class="mt-4">
        {{ $pedidos->links() }}
    </div>

    {{-- Modal: detalle del pedido --}}
    @if ($pedidoDetalle)
        <div
            class="fixed inset-0 z-50 flex items-center justify-center p-4"
            role="dialog"
            aria-modal="true"
            aria-label="Detalle del pedido {{ $pedidoDetalle->folio }}"
        >
            <div
                class="absolute inset-0 bg-slate-900/60"
                wire:click="cerrarDetalle"
                aria-hidden="true"
            ></div>

            <div class="relative max-h-[90vh] w-full max-w-2xl overflow-y-auto rounded-lg bg-white shadow-xl">
                <div class="flex items-center justify-between border-b border-slate-200 px-5 py-4">
                    <h2 class="text-base font-semibold text-slate-900">
                        Detalle del pedido
                        <span class="ml-1 font-mono text-xs text-slate-500">{{ $pedidoDetalle->folio }}</span>
                    </h2>
                    <button
                        type="button"
                        wire:click="cerrarDetalle"
                        class="rounded-md p-1.5 text-slate-400 transition hover:bg-slate-100 hover:text-slate-700"
                        aria-label="Cerrar detalle"
                    >
                        <i class="fa-solid fa-xmark" aria-hidden="true"></i>
                    </button>
                </div>

                <div class="space-y-5 px-5 py-5">
                    <dl class="grid grid-cols-2 gap-3 rounded-lg bg-slate-50 p-4 text-sm sm:grid-cols-4">
                        <div>
                            <dt class="text-xs font-medium uppercase tracking-wide text-slate-500">Correo</dt>
                            <dd class="mt-0.5 break-all font-semibold text-slate-900">{{ $pedidoDetalle->correo }}</dd>
                        </div>
                        <div>
                            <dt class="text-xs font-medium uppercase tracking-wide text-slate-500">Fecha</dt>
                            <dd class="mt-0.5 font-semibold text-slate-900">{{ \Carbon\Carbon::parse($pedidoDetalle->fecha)->format('d/m/Y H:i') }}</dd>
                        </div>
                        <div>
                            <dt class="text-xs font-medium uppercase tracking-wide text-slate-500">Estatus</dt>
                            <dd class="mt-0.5 font-semibold text-slate-900">{{ $pedidoDetalle->estatus }}</dd>
                        </div>
                        <div>
                            <dt class="text-xs font-medium uppercase tracking-wide text-slate-500">Folio</dt>
                            <dd class="mt-0.5 font-mono text-xs font-semibold text-slate-900">{{ $pedidoDetalle->folio }}</dd>
                        </div>
                    </dl>

                    <div>
                        <h3 class="text-sm font-semibold text-slate-700">Productos</h3>
                        <div class="mt-2 overflow-x-auto rounded-lg border border-slate-200">
                            <table class="min-w-full divide-y divide-slate-200 text-sm">
                                <thead class="bg-slate-50">
                                    <tr>
                                        <th scope="col" class="px-4 py-2 text-left font-semibold text-slate-700">Producto</th>
                                        <th scope="col" class="px-4 py-2 text-right font-semibold text-slate-700">Cantidad</th>
                                        <th scope="col" class="px-4 py-2 text-right font-semibold text-slate-700">Precio MXN</th>
                                        <th scope="col" class="px-4 py-2 text-right font-semibold text-slate-700">Importe</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-200">
                                    @forelse ($pedidoDetalle->detalles as $detalle)
                                        <tr>
                                            <td class="px-4 py-2">
                                                <p class="font-medium text-slate-900">{{ $detalle->producto?->nombre ?? ('Producto #' . $detalle->producto_id) }}</p>
                                                @if ($detalle->producto?->sku)
                                                    <p class="font-mono text-xs text-slate-500">{{ $detalle->producto->sku }}</p>
                                                @endif
                                            </td>
                                            <td class="whitespace-nowrap px-4 py-2 text-right text-slate-600">{{ $detalle->cantidad }}</td>
                                            <td class="whitespace-nowrap px-4 py-2 text-right text-slate-600">${{ number_format($detalle->precio_mxn, 2) }}</td>
                                            <td class="whitespace-nowrap px-4 py-2 text-right font-semibold text-slate-900">${{ number_format($detalle->cantidad * $detalle->precio_mxn, 2) }}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4" class="px-4 py-6 text-center text-slate-500">Este pedido no tiene productos.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <dl class="ml-auto w-full max-w-xs space-y-2 rounded-lg bg-slate-50 p-4 text-sm">
                        <div class="flex items-center justify-between">
                            <dt class="text-slate-500">Subtotal</dt>
                            <dd class="font-semibold text-slate-900">${{ number_format($pedidoDetalle->subtotal, 2) }}</dd>
                        </div>
                        <div class="flex items-center justify-between">
                            <dt class="text-slate-500">Impuestos</dt>
                            <dd class="font-semibold text-slate-900">${{ number_format($pedidoDetalle->impuestos, 2) }}</dd>
                        </div>
                        <div class="flex items-center justify-between border-t border-slate-200 pt-2">
                            <dt class="font-semibold text-slate-700">Total</dt>
                            <dd class="font-bold text-slate-900">${{ number_format($pedidoDetalle->total, 2) }}</dd>
                        </div>
                    </dl>
                </div>

                <div class="flex items-center justify-end border-t border-slate-200 px-5 py-4">
                    <button
                        type="button"
                        wire:click="cerrarDetalle"
                        class="rounded-md bg-indigo-600 px-4 py-2 text-sm font-semibold text-white shadow-sm transition hover:bg-indigo-500"
                    >
                        Cerrar
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>
