<section class="mx-auto max-w-7xl px-4 py-8 sm:px-8 sm:py-12">
    <h1 class="text-3xl font-bold tracking-tight text-[#112e3e] sm:text-4xl">Carrito</h1>
    <p aria-live="polite" class="mt-2 text-sm text-slate-600">
        {{ array_sum($cantidades) }} {{ array_sum($cantidades) === 1 ? 'producto' : 'productos' }}
    </p>

    @if ($items->isEmpty())
        <div class="mt-6 rounded-2xl border border-dashed border-stone-300 bg-stone-50 px-5 py-12 text-center">
            <p class="text-sm text-slate-700">Tu carrito está vacío.</p>
            <a href="{{ route('home.productos') }}" wire:navigate
                class="mt-4 inline-flex items-center justify-center gap-2 rounded-full bg-green-800 px-6 py-2.5 text-sm font-semibold text-white hover:bg-green-900 focus-visible:outline-2 focus-visible:outline-green-700">
                <i class="fa-solid fa-bag-shopping" aria-hidden="true"></i>
                Ver productos
            </a>
        </div>
    @else
        <div class="mt-6 grid gap-6 lg:grid-cols-3 lg:gap-8">
            <div class="lg:col-span-2">
                <ul class="space-y-4">
                    @foreach ($items as $item)
                        <li wire:key="carrito-{{ $item['producto']->id }}"
                            class="flex flex-col gap-4 rounded-2xl border border-stone-200 bg-white p-4 sm:flex-row sm:items-center">
                            <a href="{{ route('home.productos.detalle', $item['producto']) }}" wire:navigate
                                class="mx-auto grid aspect-[4/3] w-full max-w-52 shrink-0 place-items-center overflow-hidden rounded-xl bg-stone-50 p-2 focus-visible:outline-2 focus-visible:outline-green-700 sm:mx-0 sm:w-36"
                                aria-label="Ver {{ $item['producto']->nombre }}">
                                <img src="{{ $item['producto']->imagen ? asset('storage/' . $item['producto']->imagen) : asset('images/products/placeholder.jpg') }}"
                                    alt="" loading="lazy" class="size-full object-contain">
                            </a>

                            <div class="min-w-0 flex-1">
                                <p class="break-words font-semibold text-slate-900">
                                    <a href="{{ route('home.productos.detalle', $item['producto']) }}" wire:navigate
                                        class="rounded hover:text-green-800 hover:underline focus-visible:outline-2 focus-visible:outline-green-700">
                                        {{ $item['producto']->nombre }}
                                    </a>
                                </p>
                                <p class="mt-1 break-words text-xs text-slate-600">SKU {{ $item['producto']->sku }}</p>
                                <p class="mt-2 text-sm font-bold text-green-900 tabular-nums">
                                    ${{ number_format($item['producto']->precio_mxn, 2) }} MXN
                                </p>

                                <div class="mt-3 inline-flex items-center rounded-full border border-stone-300">
                                    <button type="button" wire:click="decrementar({{ $item['producto']->id }})"
                                        wire:loading.attr="disabled" @disabled($item['cantidad'] <= 1)
                                        aria-label="Disminuir cantidad de {{ $item['producto']->nombre }}"
                                        class="grid size-9 cursor-pointer place-items-center rounded-l-full text-green-900 enabled:hover:bg-green-50 focus-visible:outline-2 focus-visible:outline-green-700 disabled:cursor-default disabled:text-slate-400">
                                        <i class="fa-solid fa-minus text-xs" aria-hidden="true"></i>
                                    </button>
                                    <label for="cantidad-{{ $item['producto']->id }}" class="sr-only">Cantidad de
                                        {{ $item['producto']->nombre }}</label>
                                    <input id="cantidad-{{ $item['producto']->id }}" type="number"
                                        wire:model.live="cantidades.{{ $item['producto']->id }}"
                                        min="1" max="{{ $item['producto']->stock }}"
                                        class="w-14 border-x border-stone-300 py-1.5 text-center text-sm font-semibold tabular-nums focus-visible:outline-2 focus-visible:outline-green-700">
                                    <button type="button" wire:click="incrementar({{ $item['producto']->id }})"
                                        wire:loading.attr="disabled"
                                        @disabled($item['cantidad'] >= $item['producto']->stock)
                                        aria-label="Aumentar cantidad de {{ $item['producto']->nombre }}"
                                        class="grid size-9 cursor-pointer place-items-center rounded-r-full text-green-900 enabled:hover:bg-green-50 focus-visible:outline-2 focus-visible:outline-green-700 disabled:cursor-default disabled:text-slate-400">
                                        <i class="fa-solid fa-plus text-xs" aria-hidden="true"></i>
                                    </button>
                                </div>
                            </div>

                            <div class="flex items-center justify-between gap-3 sm:flex-col sm:items-end sm:justify-center">
                                <p class="text-sm font-bold text-slate-900 tabular-nums">
                                    ${{ number_format($item['linea'], 2) }} MXN</p>
                                <button type="button" wire:click="quitarDelCarrito({{ $item['producto']->id }})"
                                    wire:loading.attr="disabled"
                                    aria-label="Eliminar {{ $item['producto']->nombre }} del carrito"
                                    class="inline-flex cursor-pointer items-center gap-2 rounded-full px-3 py-2 text-sm font-semibold text-slate-500 hover:bg-red-50 hover:text-red-700 focus-visible:outline-2 focus-visible:outline-green-700">
                                    <i class="fa-solid fa-trash-can" aria-hidden="true"></i>
                                    <span class="sm:sr-only">Eliminar</span>
                                </button>
                            </div>
                        </li>
                    @endforeach
                </ul>

                <button type="button" wire:click="limpiar" wire:confirm="¿Vaciar todo el carrito?"
                    wire:loading.attr="disabled"
                    class="mt-4 inline-flex cursor-pointer items-center gap-2 rounded-full border border-stone-300 px-5 py-2.5 text-sm font-semibold text-slate-700 hover:bg-stone-50 focus-visible:outline-2 focus-visible:outline-green-700">
                    <i class="fa-solid fa-trash-can" aria-hidden="true"></i>
                    Limpiar carrito
                </button>
            </div>

            <aside aria-label="Resumen del carrito" class="lg:col-span-1">
                <div class="rounded-2xl border border-stone-200 bg-stone-50 p-5 lg:sticky lg:top-4">
                    <h2 class="text-lg font-bold text-slate-900">Resumen</h2>

                    <dl class="mt-4 space-y-2 text-sm tabular-nums">
                        <div class="flex items-center justify-between">
                            <dt class="text-slate-600">Subtotal</dt>
                            <dd class="font-semibold text-slate-900">${{ number_format($subtotal, 2) }} MXN</dd>
                        </div>
                        <div class="flex items-center justify-between">
                            <dt class="text-slate-600">IVA (16%)</dt>
                            <dd class="font-semibold text-slate-900">${{ number_format($iva, 2) }} MXN</dd>
                        </div>
                        <div class="flex items-center justify-between border-t border-stone-200 pt-3 text-base">
                            <dt class="font-bold text-slate-900">Total</dt>
                            <dd class="font-bold text-green-900">${{ number_format($total, 2) }} MXN</dd>
                        </div>
                    </dl>

                    <button type="button" disabled title="Disponible próximamente"
                        class="mt-4 flex w-full cursor-not-allowed items-center justify-center gap-2 rounded-full bg-stone-200 px-6 py-2.5 text-sm font-semibold text-slate-500">
                        <i class="fa-solid fa-lock" aria-hidden="true"></i>
                        Continuar compra
                    </button>

                    <a href="{{ route('home.productos') }}" wire:navigate
                        class="mt-2 inline-flex w-full items-center justify-center gap-2 rounded-full px-6 py-2.5 text-sm font-semibold text-green-800 hover:bg-green-50 hover:underline focus-visible:outline-2 focus-visible:outline-green-700">
                        Seguir explorando
                    </a>
                </div>
            </aside>
        </div>
    @endif
</section>
