<section class="mx-auto max-w-7xl px-4 py-8 sm:px-8 sm:py-12">
    <a href="{{ route('home.productos') }}" wire:navigate
        class="inline-flex items-center gap-2 rounded-lg text-sm font-semibold text-green-800 hover:underline focus-visible:outline-2 focus-visible:outline-green-700">
        <i class="fa-solid fa-arrow-left" aria-hidden="true"></i>
        Volver a productos
    </a>

    <h1 class="mt-4 break-words text-3xl font-bold tracking-tight text-[#112e3e] sm:text-4xl">{{ $producto->nombre }}</h1>

    <div class="mt-6 grid gap-6 lg:grid-cols-2 lg:gap-8">
        <div class="flex aspect-[4/3] items-center justify-center rounded-2xl border border-stone-200 bg-stone-50 p-4">
            <img src="{{ $producto->imagen ? asset('storage/' . $producto->imagen) : asset('images/products/placeholder.jpg') }}"
                alt="{{ $producto->imagen ? $producto->nombre : '' }}"
                class="size-full object-contain">
        </div>

        <div class="min-w-0">
            @if ($producto->descripcion_corta)
                <p class="break-words text-base leading-relaxed font-medium text-slate-900">{{ $producto->descripcion_corta }}</p>
            @endif

            @if ($producto->descripcion_larga)
                <div class="mt-4 rounded-2xl border border-stone-200 bg-stone-50 p-4">
                    <p class="text-xs font-semibold tracking-wide text-slate-500 uppercase">Descripción</p>
                    <p class="mt-2 break-words text-sm leading-relaxed text-slate-700">{{ $producto->descripcion_larga }}</p>
                </div>
            @endif

            <p class="mt-4 break-words text-xs text-slate-600">SKU {{ $producto->sku }}</p>

            <div class="mt-2 flex flex-wrap items-baseline gap-x-3 gap-y-1 tabular-nums">
                <p class="text-2xl font-bold text-green-900">${{ number_format($producto->precio_mxn, 2) }} MXN</p>
                <p class="text-sm text-slate-600">${{ number_format($producto->precio_usd, 2) }} USD</p>
            </div>

            <div class="mt-4">
                @if ($producto->stock > 0)
                    @if ($producto->stock <= 10)
                        <p class="mt-1 inline-flex items-center gap-2 rounded-full bg-amber-50 px-3 py-1 text-sm font-semibold text-amber-800">
                            <i class="fa-solid fa-triangle-exclamation" aria-hidden="true"></i>
                            Solo quedan {{ $producto->stock }} {{ $producto->stock === 1 ? 'pieza' : 'piezas' }}
                        </p>
                    @else
                        <p class="text-sm text-slate-700">Stock disponible: <span class="font-semibold">{{ $producto->stock }}</span> {{ $producto->stock === 1 ? 'pieza' : 'piezas' }}</p>
                    @endif
                @else
                    <p class="inline-flex items-center gap-2 rounded-full bg-red-50 px-3 py-1 text-sm font-semibold text-red-800">
                        <i class="fa-solid fa-circle-xmark" aria-hidden="true"></i>
                        Sin stock por el momento
                    </p>
                @endif
            </div>

            <div class="mt-6 flex flex-wrap items-center gap-3">
                <div class="inline-flex items-center rounded-full border border-stone-300">
                    <button type="button" wire:click="decrementar" @disabled($producto->stock <= 0 || $cantidad <= 1)
                        aria-label="Disminuir cantidad"
                        class="grid size-10 cursor-pointer place-items-center rounded-l-full text-green-900 enabled:hover:bg-green-50 focus-visible:outline-2 focus-visible:outline-green-700 disabled:cursor-default disabled:text-slate-400">
                        <i class="fa-solid fa-minus text-xs" aria-hidden="true"></i>
                    </button>
                    <label for="cantidad" class="sr-only">Cantidad</label>
                    <input id="cantidad" type="number" wire:model.live="cantidad"
                        min="{{ $producto->stock > 0 ? 1 : 0 }}" max="{{ $producto->stock }}"
                        @disabled($producto->stock <= 0)
                        class="w-16 border-x border-stone-300 py-2 text-center text-sm font-semibold tabular-nums focus-visible:outline-2 focus-visible:outline-green-700 disabled:bg-stone-50 disabled:text-slate-500">
                    <button type="button" wire:click="incrementar" @disabled($producto->stock <= 0 || $cantidad >= $producto->stock)
                        aria-label="Aumentar cantidad"
                        class="grid size-10 cursor-pointer place-items-center rounded-r-full text-green-900 enabled:hover:bg-green-50 focus-visible:outline-2 focus-visible:outline-green-700 disabled:cursor-default disabled:text-slate-400">
                        <i class="fa-solid fa-plus text-xs" aria-hidden="true"></i>
                    </button>
                </div>

                <button type="button" wire:click="agregarAlCarrito" wire:loading.attr="disabled"
                    @disabled($producto->stock <= 0)
                    class="inline-flex flex-1 cursor-pointer items-center justify-center gap-2 rounded-full bg-green-800 px-6 py-2.5 text-sm font-semibold text-white hover:bg-green-900 focus-visible:outline-2 focus-visible:outline-green-700 disabled:cursor-default disabled:bg-stone-100 disabled:text-slate-500 sm:flex-none sm:px-8">
                    <i class="fa-solid fa-cart-plus" aria-hidden="true"></i>
                    Agregar al carrito
                </button>
            </div>
        </div>
    </div>
</section>
