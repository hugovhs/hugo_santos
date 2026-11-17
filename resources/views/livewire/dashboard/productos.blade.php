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

        <div class="flex flex-col gap-3 sm:flex-row sm:items-center">
            <div class="relative">
                <i class="fa-solid fa-magnifying-glass pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 text-slate-400" aria-hidden="true"></i>
                <input
                    type="search"
                    wire:model.live.debounce.300ms="search"
                    placeholder="Buscar por nombre o SKU..."
                    aria-label="Buscar productos por nombre o SKU"
                    class="w-full rounded-md border border-slate-300 bg-white py-2 pl-9 pr-3 text-sm text-slate-900 shadow-sm placeholder:text-slate-400 focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500 sm:w-72"
                />
            </div>

            <a
                href="{{ route('dashboard.productos.crear') }}"
                class="inline-flex items-center justify-center gap-2 rounded-md bg-indigo-600 px-4 py-2 text-sm font-semibold text-white shadow-sm transition hover:bg-indigo-500"
            >
                <i class="fa-solid fa-plus" aria-hidden="true"></i>
                Agregar producto
            </a>
        </div>
    </div>

    {{-- Tabla de productos --}}
    <div class="overflow-hidden rounded-lg border border-slate-200 bg-white shadow-sm">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200 text-sm">
                <thead class="bg-slate-50">
                    <tr>
                        <th scope="col" class="px-4 py-3 text-left font-semibold text-slate-700">Imagen</th>
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
                            <td class="whitespace-nowrap px-4 py-3">
                                <img
                                    src="{{ $producto->imagen ? asset('storage/' . $producto->imagen) : asset('images/products/placeholder.jpg') }}"
                                    alt="Imagen de {{ $producto->nombre }}"
                                    loading="lazy"
                                    onerror="this.onerror=null;this.src='{{ asset('images/products/placeholder.jpg') }}';"
                                    class="h-10 w-10 rounded-md border border-slate-200 bg-slate-50 object-cover"
                                >
                            </td>
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
                                <div class="flex items-center justify-center gap-2">
                                    <button
                                        type="button"
                                        wire:click="verDetalle({{ $producto->id }})"
                                        title="Ver detalle"
                                        class="rounded-md p-1.5 text-slate-500 transition hover:bg-slate-100 hover:text-indigo-600"
                                    >
                                        <i class="fa-solid fa-eye" aria-hidden="true"></i>
                                        <span class="sr-only">Ver detalle</span>
                                    </button>
                                    <a
                                        href="{{ route('dashboard.productos.editar', $producto) }}"
                                        title="Editar"
                                        class="rounded-md p-1.5 text-slate-500 transition hover:bg-slate-100 hover:text-indigo-600"
                                    >
                                        <i class="fa-solid fa-pen" aria-hidden="true"></i>
                                        <span class="sr-only">Editar</span>
                                    </a>
                                    <button
                                        type="button"
                                        wire:click="confirmarToggleActivo({{ $producto->id }})"
                                        title="{{ $producto->activo ? 'Desactivar' : 'Activar' }}"
                                        class="rounded-md p-1.5 text-slate-500 transition hover:bg-slate-100 {{ $producto->activo ? 'hover:text-rose-600' : 'hover:text-emerald-600' }}"
                                    >
                                        <i class="fa-solid fa-power-off" aria-hidden="true"></i>
                                        <span class="sr-only">{{ $producto->activo ? 'Desactivar' : 'Activar' }}</span>
                                    </button>
                                    <button
                                        type="button"
                                        wire:click="confirmarEliminar({{ $producto->id }})"
                                        title="Eliminar"
                                        class="rounded-md p-1.5 text-slate-500 transition hover:bg-slate-100 hover:text-rose-600"
                                    >
                                        <i class="fa-solid fa-trash" aria-hidden="true"></i>
                                        <span class="sr-only">Eliminar</span>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-4 py-10 text-center text-slate-500">
                                @if (trim($search) !== '')
                                    No se encontraron productos para «{{ $search }}».
                                @else
                                    No hay productos registrados.
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
        {{ $productos->links() }}
    </div>

    {{-- Modal: detalle del producto --}}
    @if ($productoDetalle)
        <div
            class="fixed inset-0 z-50 flex items-center justify-center p-4"
            role="dialog"
            aria-modal="true"
            aria-label="Detalle del producto {{ $productoDetalle->nombre }}"
        >
            <div
                class="absolute inset-0 bg-slate-900/60"
                wire:click="cerrarDetalle"
                aria-hidden="true"
            ></div>

            <div class="relative max-h-[90vh] w-full max-w-lg overflow-y-auto rounded-lg bg-white shadow-xl">
                <div class="flex items-center justify-between border-b border-slate-200 px-5 py-4">
                    <h2 class="text-base font-semibold text-slate-900">
                        Detalle del producto
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
                    <div class="flex flex-col items-center text-center">
                        <img
                            src="{{ $productoDetalle->imagen ? asset('storage/' . $productoDetalle->imagen) : asset('images/products/placeholder.jpg') }}"
                            alt="Imagen de {{ $productoDetalle->nombre }}"
                            onerror="this.onerror=null;this.src='{{ asset('images/products/placeholder.jpg') }}';"
                            class="h-48 w-48 rounded-lg border border-slate-200 bg-slate-50 object-cover shadow-sm"
                        >
                        <p class="mt-3 text-lg font-semibold text-slate-900">{{ $productoDetalle->nombre }}</p>
                        <div class="mt-2 flex items-center gap-2">
                            <span class="rounded-md bg-slate-100 px-2 py-0.5 font-mono text-xs text-slate-600">{{ $productoDetalle->sku }}</span>
                            @if ($productoDetalle->activo)
                                <span class="inline-flex items-center rounded-full bg-emerald-100 px-2.5 py-0.5 text-xs font-medium text-emerald-700">Activo</span>
                            @else
                                <span class="inline-flex items-center rounded-full bg-slate-100 px-2.5 py-0.5 text-xs font-medium text-slate-600">Inactivo</span>
                            @endif
                        </div>
                    </div>

                    <div>
                        <h3 class="text-sm font-semibold text-slate-700">Descripción corta</h3>
                        <p class="mt-1 text-sm text-slate-600">{{ $productoDetalle->descripcion_corta ?: '—' }}</p>
                    </div>

                    <div>
                        <h3 class="text-sm font-semibold text-slate-700">Descripción larga</h3>
                        <p class="mt-1 whitespace-pre-line text-sm text-slate-600">{{ $productoDetalle->descripcion_larga ?: '—' }}</p>
                    </div>

                    <dl class="grid grid-cols-2 gap-3 rounded-lg bg-slate-50 p-4 text-sm">
                        <div>
                            <dt class="text-xs font-medium uppercase tracking-wide text-slate-500">Precio USD</dt>
                            <dd class="mt-0.5 font-semibold text-slate-900">${{ number_format($productoDetalle->precio_usd, 2) }}</dd>
                        </div>
                        <div>
                            <dt class="text-xs font-medium uppercase tracking-wide text-slate-500">Precio MXN</dt>
                            <dd class="mt-0.5 font-semibold text-slate-900">${{ number_format($productoDetalle->precio_mxn, 2) }}</dd>
                        </div>
                        <div>
                            <dt class="text-xs font-medium uppercase tracking-wide text-slate-500">Stock</dt>
                            <dd class="mt-0.5 font-semibold text-slate-900">{{ $productoDetalle->stock }}</dd>
                        </div>
                        <div>
                            <dt class="text-xs font-medium uppercase tracking-wide text-slate-500">Vigencia</dt>
                            <dd class="mt-0.5 font-semibold text-slate-900">{{ $productoDetalle->fecha_vigencia ? \Carbon\Carbon::parse($productoDetalle->fecha_vigencia)->format('d/m/Y') : '—' }}</dd>
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

    {{-- confirmación activar/desactivar --}}
    @if ($productoToggle)
        <div
            class="fixed inset-0 z-50 flex items-center justify-center p-4"
            role="dialog"
            aria-modal="true"
            aria-label="Confirmar cambio de estatus del producto {{ $productoToggle->nombre }}"
        >
            <div
                class="absolute inset-0 bg-slate-900/60"
                wire:click="cancelarToggleActivo"
                aria-hidden="true"
            ></div>

            <div class="relative w-full max-w-md rounded-lg bg-white shadow-xl">
                <div class="flex items-center justify-between border-b border-slate-200 px-5 py-4">
                    <h2 class="text-base font-semibold text-slate-900">
                        {{ $productoToggle->activo ? 'Desactivar producto' : 'Activar producto' }}
                    </h2>
                    <button
                        type="button"
                        wire:click="cancelarToggleActivo"
                        class="rounded-md p-1.5 text-slate-400 transition hover:bg-slate-100 hover:text-slate-700"
                        aria-label="Cerrar confirmación"
                    >
                        <i class="fa-solid fa-xmark" aria-hidden="true"></i>
                    </button>
                </div>

                <div class="space-y-4 px-5 py-5 text-sm text-slate-600">
                    <p>
                        ¿Confirmas cambiar el estatus del producto
                        <span class="font-semibold text-slate-900">«{{ $productoToggle->nombre }}»</span>
                        <span class="font-mono text-xs text-slate-500">({{ $productoToggle->sku }})</span>?
                    </p>

                    <dl class="grid grid-cols-[1fr_auto_1fr] items-center gap-3 rounded-lg bg-slate-50 p-4 text-center">
                        <div>
                            <dt class="text-xs font-medium uppercase tracking-wide text-slate-500">Estatus actual</dt>
                            <dd class="mt-1.5">
                                @if ($productoToggle->activo)
                                    <span class="inline-flex items-center rounded-full bg-emerald-100 px-2.5 py-0.5 text-xs font-medium text-emerald-700">Activo</span>
                                @else
                                    <span class="inline-flex items-center rounded-full bg-slate-100 px-2.5 py-0.5 text-xs font-medium text-slate-600">Inactivo</span>
                                @endif
                            </dd>
                        </div>
                        <div aria-hidden="true" class="text-slate-400">
                            <i class="fa-solid fa-arrow-right"></i>
                        </div>
                        <div>
                            <dt class="text-xs font-medium uppercase tracking-wide text-slate-500">Nuevo estatus</dt>
                            <dd class="mt-1.5">
                                @if ($productoToggle->activo)
                                    <span class="inline-flex items-center rounded-full bg-slate-100 px-2.5 py-0.5 text-xs font-medium text-slate-600">Inactivo</span>
                                @else
                                    <span class="inline-flex items-center rounded-full bg-emerald-100 px-2.5 py-0.5 text-xs font-medium text-emerald-700">Activo</span>
                                @endif
                            </dd>
                        </div>
                    </dl>
                </div>

                <div class="flex items-center justify-end gap-3 border-t border-slate-200 px-5 py-4">
                    <button
                        type="button"
                        wire:click="cancelarToggleActivo"
                        class="rounded-md border border-slate-300 bg-white px-4 py-2 text-sm font-semibold text-slate-700 shadow-sm transition hover:bg-slate-50"
                    >
                        Cancelar
                    </button>
                    <button
                        type="button"
                        wire:click="toggleActivo"
                        class="rounded-md px-4 py-2 text-sm font-semibold text-white shadow-sm transition {{ $productoToggle->activo ? 'bg-rose-600 hover:bg-rose-500' : 'bg-emerald-600 hover:bg-emerald-500' }}"
                    >
                        {{ $productoToggle->activo ? 'Sí, desactivar' : 'Sí, activar' }}
                    </button>
                </div>
            </div>
        </div>
    @endif

    {{-- confirmación eliminar --}}
    @if ($productoEliminar)
        <div
            class="fixed inset-0 z-50 flex items-center justify-center p-4"
            role="dialog"
            aria-modal="true"
            aria-label="¿Eliminar el producto {{ $productoEliminar->nombre }}?"
        >
            <div
                class="absolute inset-0 bg-slate-900/60"
                wire:click="cancelarEliminar"
                aria-hidden="true"
            ></div>

            <div class="relative w-full max-w-md rounded-lg bg-white shadow-xl">
                <div class="flex items-center justify-between border-b border-slate-200 px-5 py-4">
                    <h2 class="text-base font-semibold text-slate-900">
                        Eliminar producto
                    </h2>
                    <button
                        type="button"
                        wire:click="cancelarEliminar"
                        class="rounded-md p-1.5 text-slate-400 transition hover:bg-slate-100 hover:text-slate-700"
                        aria-label="Cerrar confirmación"
                    >
                        <i class="fa-solid fa-xmark" aria-hidden="true"></i>
                    </button>
                </div>

                <div class="space-y-4 px-5 py-5 text-sm text-slate-600">
                    <p>
                        ¿Deseas eliminar el producto
                        <span class="font-semibold text-slate-900">«{{ $productoEliminar->nombre }}»</span>
                        <span class="font-mono text-xs text-slate-500">({{ $productoEliminar->sku }})</span>?
                    </p>
                    <p class="text-xs text-slate-500">
                        El producto dejará de mostrarse en el catálogo.
                    </p>
                </div>

                <div class="flex items-center justify-end gap-3 border-t border-slate-200 px-5 py-4">
                    <button
                        type="button"
                        wire:click="cancelarEliminar"
                        class="rounded-md border border-slate-300 bg-white px-4 py-2 text-sm font-semibold text-slate-700 shadow-sm transition hover:bg-slate-50"
                    >
                        Cancelar
                    </button>
                    <button
                        type="button"
                        wire:click="eliminar"
                        class="rounded-md bg-rose-600 px-4 py-2 text-sm font-semibold text-white shadow-sm transition hover:bg-rose-500"
                    >
                        Sí, eliminar
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>
