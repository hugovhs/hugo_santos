<div role="status" aria-label="Carrito, {{ $total }} {{ $total === 1 ? 'producto' : 'productos' }}"
    class="order-1 inline-flex items-center gap-2 justify-self-end rounded-full bg-green-50 px-3 py-2.5 text-green-800 sm:order-3 sm:px-4 lg:order-4">
    <i class="fa-solid fa-cart-shopping text-sm" aria-hidden="true"></i>
    <span aria-hidden="true"
        class="min-w-5 rounded-full bg-green-800 px-1 text-center text-xs leading-5 font-semibold text-white tabular-nums">{{ $total }}</span>
</div>
