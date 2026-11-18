<div x-data="{ abierto: false }" @keydown.escape.window="abierto = false"
    class="relative order-1 justify-self-end sm:order-3 lg:order-4">
    <button type="button" @click="abierto = !abierto" aria-controls="minicart-panel"
        :aria-expanded="abierto.toString()" aria-label="Carrito, {{ $total }} {{ $total === 1 ? 'producto' : 'productos' }}"
        class="inline-flex cursor-pointer items-center gap-2 rounded-full bg-green-50 px-3 py-2.5 text-green-800 hover:bg-green-100 focus-visible:outline-2 focus-visible:outline-green-700 sm:px-4">
        <i class="fa-solid fa-cart-shopping text-sm" aria-hidden="true"></i>
        <span aria-hidden="true"
            class="min-w-5 rounded-full bg-green-800 px-1 text-center text-xs leading-5 font-semibold text-white tabular-nums">{{ $total }}</span>
    </button>

    <div id="minicart-panel" x-show="abierto" @click.outside="abierto = false" x-transition style="display: none;"
        role="dialog" aria-label="Minicarrito"
        class="absolute right-0 z-30 mt-2 w-80 max-w-[calc(100vw-2rem)] rounded-2xl border border-stone-200 bg-white p-4 shadow-xl">
        <p class="text-sm font-bold text-slate-900">
            Tu carrito
            <span class="font-semibold text-slate-600">({{ $total }} {{ $total === 1 ? 'producto' : 'productos' }})</span>
        </p>

        @if ($items->isNotEmpty())
            <ul class="mt-3 max-h-72 space-y-3 overflow-y-auto">
                @foreach ($items as $item)
                    <li wire:key="minicart-{{ $item['producto']->id }}" class="flex gap-3">
                        <a href="{{ route('home.productos.detalle', $item['producto']) }}" wire:navigate
                            @click="abierto = false"
                            class="grid size-14 shrink-0 place-items-center overflow-hidden rounded-xl bg-stone-50 focus-visible:outline-2 focus-visible:outline-green-700"
                            aria-label="Ver {{ $item['producto']->nombre }}">
                            <img src="{{ $item['producto']->imagen ? asset('storage/' . $item['producto']->imagen) : asset('images/products/placeholder.jpg') }}"
                                alt="" loading="lazy" class="size-full object-contain">
                        </a>

                        <div class="min-w-0 flex-1">
                            <p class="truncate text-sm font-semibold text-slate-900">{{ $item['producto']->nombre }}</p>
                            <p class="truncate text-xs text-slate-600">SKU {{ $item['producto']->sku }}</p>
                            <p class="mt-1 text-sm text-slate-700 tabular-nums">
                                {{ $item['cantidad'] }} × ${{ number_format($item['producto']->precio_mxn, 2) }} MXN
                            </p>
                        </div>

                        <button type="button" wire:click="quitarDelCarrito({{ $item['producto']->id }})"
                            wire:loading.attr="disabled"
                            aria-label="Eliminar {{ $item['producto']->nombre }} del carrito"
                            class="grid size-8 shrink-0 cursor-pointer place-items-center self-start rounded-full text-slate-500 hover:bg-red-50 hover:text-red-700 focus-visible:outline-2 focus-visible:outline-green-700">
                            <i class="fa-solid fa-trash-can text-sm" aria-hidden="true"></i>
                        </button>
                    </li>
                @endforeach
            </ul>

            <p class="mt-3 flex items-center justify-between border-t border-stone-200 pt-3 text-sm tabular-nums">
                <span class="font-semibold text-slate-700">Subtotal</span>
                <span class="font-bold text-green-900">${{ number_format($subtotal, 2) }} MXN</span>
            </p>

            <div class="mt-3 grid grid-cols-2 gap-2">
                <a href="{{ route('home.carrito') }}" wire:navigate @click="abierto = false"
                    class="inline-flex items-center justify-center rounded-full bg-green-800 px-3 py-1.5 text-sm font-semibold text-white hover:bg-green-900 focus-visible:outline-2 focus-visible:outline-green-700">
                    Ver carrito
                </a>
                <button type="button" disabled title="Disponible próximamente"
                    class="inline-flex cursor-not-allowed items-center justify-center rounded-full bg-stone-100 px-3 py-1.5 text-sm font-semibold text-slate-500">
                    Seguir con la compra
                </button>
            </div>
        @else
            <p class="mt-3 rounded-xl bg-stone-50 px-4 py-6 text-center text-sm text-slate-600">
                Tu carrito está vacío.
            </p>
            <a href="{{ route('home.productos') }}" wire:navigate @click="abierto = false"
                class="mt-3 inline-flex w-full items-center justify-center rounded-full bg-green-800 px-3 py-1.5 text-sm font-semibold text-white hover:bg-green-900 focus-visible:outline-2 focus-visible:outline-green-700">
                Ver productos
            </a>
        @endif
    </div>
</div>
