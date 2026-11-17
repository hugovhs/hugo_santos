<div>
    {{-- Mensaje success (se oculta automáticamente con Alpine) --}}
    @if (session('success'))
        <div
            x-data="{ show: true }"
            x-init="setTimeout(() => { show = false }, 5000)"
            x-show="show"
            x-transition.opacity.duration.300ms
            role="status"
            class="mb-4 flex items-center justify-between gap-4 rounded-md border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800"
        >
            <span>
                <i class="fa-solid fa-circle-check mr-2" aria-hidden="true"></i>
                {{ session('success') }}
            </span>
            <button type="button" @click="show = false" class="text-emerald-600 transition hover:text-emerald-800">
                <i class="fa-solid fa-xmark" aria-hidden="true"></i>
                <span class="sr-only">Cerrar</span>
            </button>
        </div>
    @endif

    <div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-xl font-semibold text-slate-900">Productos</h1>
            <p class="mt-1 text-sm text-slate-500">Administrar catálogo de productos.</p>
        </div>

        <a
            href="{{ route('dashboard.productos.crear') }}"
            class="inline-flex items-center gap-2 rounded-md bg-indigo-600 px-4 py-2 text-sm font-semibold text-white shadow-sm transition hover:bg-indigo-500"
        >
            <i class="fa-solid fa-plus" aria-hidden="true"></i>
            Agregar producto
        </a>
    </div>

    {{-- Tabla de productos --}}
    <div class="overflow-hidden rounded-lg border border-slate-200 bg-white shadow-sm">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200 text-sm">
                <thead class="bg-slate-50">
                    <tr>
                        <th scope="col" class="px-4 py-3 text-left font-semibold text-slate-700">SKU</th>
                        <th scope="col" class="px-4 py-3 text-left font-semibold text-slate-700">Nombre</th>
                        <th scope="col" class="px-4 py-3 text-right font-semibold text-slate-700">Precio USD</th>
                        <th scope="col" class="px-4 py-3 text-right font-semibold text-slate-700">Precio MXN</th>
                        <th scope="col" class="px-4 py-3 text-right font-semibold text-slate-700">Stock</th>
                        <th scope="col" class="px-4 py-3 text-center font-semibold text-slate-700">Estatus</th>
                        <th scope="col" class="px-4 py-3 text-center font-semibold text-slate-700">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200">
                    @forelse ($productos as $producto)
                        <tr class="hover:bg-slate-50">
                            <td class="whitespace-nowrap px-4 py-3 font-mono text-xs text-slate-600">{{ $producto->sku }}</td>
                            <td class="px-4 py-3 font-medium text-slate-900">{{ $producto->nombre }}</td>
                            <td class="whitespace-nowrap px-4 py-3 text-right text-slate-600">${{ number_format($producto->precio_usd, 2) }}</td>
                            <td class="whitespace-nowrap px-4 py-3 text-right text-slate-600">${{ number_format($producto->precio_mxn, 2) }}</td>
                            <td class="whitespace-nowrap px-4 py-3 text-right text-slate-600">{{ $producto->stock }}</td>
                            <td class="whitespace-nowrap px-4 py-3 text-center">
                                @if ($producto->activo)
                                    <span class="inline-flex items-center rounded-full bg-emerald-100 px-2.5 py-0.5 text-xs font-medium text-emerald-700">Activo</span>
                                @else
                                    <span class="inline-flex items-center rounded-full bg-slate-100 px-2.5 py-0.5 text-xs font-medium text-slate-600">Inactivo</span>
                                @endif
                            </td>
                            <td class="whitespace-nowrap px-4 py-3">
                                {{-- TODO: conectar acciones (ver detalle, editar, activar/inactivar) --}}
                                <div class="flex items-center justify-center gap-2">
                                    <button
                                        type="button"
                                        disabled
                                        title="Ver detalle"
                                        class="rounded-md p-1.5 text-slate-500 transition hover:bg-slate-100 hover:text-indigo-600 disabled:cursor-not-allowed disabled:opacity-50"
                                    >
                                        <i class="fa-solid fa-eye" aria-hidden="true"></i>
                                        <span class="sr-only">Ver detalle</span>
                                    </button>
                                    <button
                                        type="button"
                                        disabled
                                        title="Editar"
                                        class="rounded-md p-1.5 text-slate-500 transition hover:bg-slate-100 hover:text-indigo-600 disabled:cursor-not-allowed disabled:opacity-50"
                                    >
                                        <i class="fa-solid fa-pen" aria-hidden="true"></i>
                                        <span class="sr-only">Editar</span>
                                    </button>
                                    <button
                                        type="button"
                                        disabled
                                        title="{{ $producto->activo ? 'Inactivar' : 'Activar' }}"
                                        class="rounded-md p-1.5 text-slate-500 transition hover:bg-slate-100 {{ $producto->activo ? 'hover:text-rose-600' : 'hover:text-emerald-600' }} disabled:cursor-not-allowed disabled:opacity-50"
                                    >
                                        <i class="fa-solid fa-power-off" aria-hidden="true"></i>
                                        <span class="sr-only">{{ $producto->activo ? 'Inactivar' : 'Activar' }}</span>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-4 py-10 text-center text-slate-500">
                                No hay productos registrados.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Paginación --}}
    <div class="mt-4">
        {{ $productos->links() }}
    </div>
</div>
