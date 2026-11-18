<section class="mx-auto max-w-7xl px-4 py-8 sm:px-8 sm:py-12">
    <div class="flex flex-col gap-6 md:flex-row md:items-start lg:gap-8">
        <aside aria-label="Filtros de productos" class="rounded-2xl border border-stone-200 bg-stone-50 p-5 md:w-48 md:shrink-0 lg:w-60">
            <label class="block text-sm font-semibold">
                Buscar en el catálogo
                <input type="search" wire:model.live.debounce.300ms="busqueda" placeholder="Nombre o SKU"
                    class="mt-2 block w-full rounded-lg border border-stone-300 bg-white px-3 py-2.5 text-sm font-normal placeholder:text-slate-500 focus-visible:outline-2 focus-visible:outline-green-700">
            </label>

            <fieldset class="mt-5 min-w-0 border-t border-stone-200 pt-4">
                <legend class="sr-only">Categorías</legend>
                <p aria-hidden="true" class="mb-2 text-sm font-semibold">Categorías</p>

                <ul class="max-h-72 space-y-1 overflow-y-auto">
                    @forelse ($categorias as $categoria)
                        <li wire:key="filtro-categoria-{{ $categoria->id }}">
                            <label class="flex cursor-pointer items-start gap-2.5 rounded-lg px-2 py-2 text-sm text-slate-700 hover:bg-green-50">
                                <input type="checkbox" value="{{ $categoria->id }}" wire:model.live="categoriasSeleccionadas"
                                    @checked(in_array((string) $categoria->id, $categoriasSeleccionadas, true))
                                    class="mt-0.5 size-4 shrink-0 accent-green-800 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-green-700">
                                <span class="min-w-0 break-words">{{ $categoria->nombre }}</span>
                            </label>
                        </li>
                    @empty
                        <li class="text-sm text-slate-600">Aún no hay categorías.</li>
                    @endforelse
                </ul>
            </fieldset>
        </aside>

        <section aria-label="Resultados del catálogo" class="min-w-0 flex-1">
            <div class="mb-5">
                <h1 class="text-3xl font-bold tracking-tight text-[#112e3e] sm:text-4xl">Productos</h1>
                <p aria-live="polite" class="mt-2 text-sm text-slate-600">
                    {{ $productos->total() }} {{ $productos->total() === 1 ? 'producto' : 'productos' }}
                </p>
            </div>

            <div wire:loading.class="opacity-60">
                @if ($productos->isNotEmpty())
                    <ul class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                        @foreach ($productos as $producto)
                            <li wire:key="producto-{{ $producto->id }}" class="flex min-w-0 flex-col overflow-hidden rounded-2xl border border-stone-200 bg-white">
                                <div class="flex aspect-[4/3] items-center justify-center bg-stone-50 p-4">
                                    <img src="{{ $producto->imagen ? asset('storage/' . $producto->imagen) : asset('images/products/placeholder.jpg') }}"
                                        alt="{{ $producto->imagen ? $producto->nombre : '' }}" loading="lazy"
                                        class="size-full object-contain">
                                </div>

                                <div class="flex flex-1 flex-col p-4">
                                    <h2 class="break-words font-semibold text-slate-900">{{ $producto->nombre }}</h2>
                                    <p class="mt-1 break-words text-xs text-slate-600">SKU {{ $producto->sku }}</p>

                                    <div class="mt-auto pt-4">
                                        <div class="flex flex-wrap items-baseline gap-x-3 gap-y-1 tabular-nums">
                                            <p class="whitespace-nowrap font-bold text-green-900">${{ number_format($producto->precio_mxn, 2) }} MXN</p>
                                            <p class="text-sm whitespace-nowrap text-slate-600">${{ number_format($producto->precio_usd, 2) }} USD</p>
                                        </div>

                                        <button type="button" disabled
                                            class="mt-4 flex w-full items-center justify-center gap-2 rounded-full bg-green-50 px-3 py-2.5 text-sm font-semibold text-green-900">
                                            <i class="fa-solid fa-cart-plus" aria-hidden="true"></i>
                                            Añadir al carrito
                                        </button>
                                    </div>
                                </div>
                            </li>
                        @endforeach
                    </ul>
                @else
                    <p class="rounded-2xl border border-dashed border-stone-300 bg-stone-50 px-5 py-10 text-center text-sm text-slate-700">
                        No encontramos productos con estos filtros.
                    </p>
                @endif
            </div>

            @if ($productos->hasPages())
                <nav aria-label="Paginación de productos" class="mt-6 flex flex-wrap items-center justify-center gap-3 text-sm">
                    <button type="button" wire:click="previousPage" wire:loading.attr="disabled"
                        x-on:click="$el.closest('section').scrollIntoView()"
                        @disabled($productos->onFirstPage())
                        class="cursor-pointer rounded-full border border-stone-300 px-4 py-2 font-semibold text-green-900 enabled:hover:bg-green-50 focus-visible:outline-2 focus-visible:outline-green-700 disabled:cursor-default disabled:text-slate-500">
                        Anterior
                    </button>
                    <span class="text-slate-600" aria-live="polite">Página {{ $productos->currentPage() }} de {{ $productos->lastPage() }}</span>
                    <button type="button" wire:click="nextPage" wire:loading.attr="disabled"
                        x-on:click="$el.closest('section').scrollIntoView()"
                        @disabled(! $productos->hasMorePages())
                        class="cursor-pointer rounded-full border border-stone-300 px-4 py-2 font-semibold text-green-900 enabled:hover:bg-green-50 focus-visible:outline-2 focus-visible:outline-green-700 disabled:cursor-default disabled:text-slate-500">
                        Siguiente
                    </button>
                </nav>
            @endif
        </section>
    </div>
</section>
