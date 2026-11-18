<section class="mx-auto max-w-2xl px-4 py-12 text-center sm:px-8 sm:py-16">
    <div class="rounded-3xl border border-stone-200 bg-white p-8 shadow-sm sm:p-12">
        <span class="mx-auto grid size-16 place-items-center rounded-full bg-green-100 text-2xl text-green-800">
            <i class="fa-solid fa-check" aria-hidden="true"></i>
        </span>

        <h1 class="mt-6 text-3xl font-bold tracking-tight text-[#112e3e] sm:text-4xl">¡Orden generada!</h1>

        <p class="mt-3 text-sm text-slate-600">Folio de tu pedido</p>
        <p class="mt-1 inline-block rounded-full bg-stone-100 px-5 py-2 font-mono text-lg font-bold tracking-wide text-green-900">{{ $pedido->folio }}</p>

        <dl class="mx-auto mt-6 max-w-sm space-y-2 rounded-2xl bg-stone-50 p-5 text-sm tabular-nums">
            <div class="flex items-center justify-between">
                <dt class="text-slate-600">Total pagado</dt>
                <dd class="font-bold text-green-900">${{ number_format($pedido->total, 2) }} MXN</dd>
            </div>
            <div class="flex items-center justify-between">
                <dt class="text-slate-600">Correo</dt>
                <dd class="font-semibold text-slate-900">{{ $pedido->correo }}</dd>
            </div>
        </dl>

        <p class="mt-6 text-sm leading-relaxed text-slate-600">
            Tu orden será enviada a la brevedad, mantente al tanto de tu correo.
            Cualquier duda puedes contactarnos por WhatsApp o correo.
        </p>

        <div class="mt-8 flex flex-col justify-center gap-3 sm:flex-row">
            <a href="{{ route('home.productos') }}" wire:navigate
                class="inline-flex items-center justify-center gap-2 rounded-full bg-green-800 px-6 py-2.5 text-sm font-semibold text-white hover:bg-green-900 focus-visible:outline-2 focus-visible:outline-green-700">
                <i class="fa-solid fa-bag-shopping" aria-hidden="true"></i>
                Seguir comprando
            </a>
            <a href="{{ route('home.index') }}" wire:navigate
                class="inline-flex items-center justify-center gap-2 rounded-full border border-stone-300 px-6 py-2.5 text-sm font-semibold text-slate-700 hover:bg-stone-50 focus-visible:outline-2 focus-visible:outline-green-700">
                Volver al inicio
            </a>
        </div>
    </div>
</section>
