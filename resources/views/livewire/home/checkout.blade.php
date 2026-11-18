<section class="mx-auto max-w-7xl px-4 py-8 sm:px-8 sm:py-12">
    <h1 class="text-3xl font-bold tracking-tight text-[#112e3e] sm:text-4xl">Checkout</h1>
    <p aria-live="polite" class="mt-2 text-sm text-slate-600">
        {{ array_sum(array_column($items->toArray(), 'cantidad')) }} {{ array_sum(array_column($items->toArray(), 'cantidad')) === 1 ? 'producto' : 'productos' }} en tu pedido
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
            {{-- Columna izquierda: pasos --}}
            <div class="space-y-6 lg:col-span-2">

                {{-- Paso 1: correo --}}
                <div class="rounded-2xl border border-stone-200 bg-white p-5">
                    <div class="flex items-center gap-3">
                        <span class="grid size-8 shrink-0 place-items-center rounded-full text-sm font-bold {{ $correoConfirmado ? 'bg-green-800 text-white' : 'bg-green-100 text-green-900' }}">1</span>
                        <h2 class="text-lg font-bold text-slate-900">Tu correo</h2>
                    </div>

                    @if (! $correoConfirmado)
                        <form wire:submit="continuarConCorreo" class="mt-4">
                            <label for="correo" class="text-sm font-semibold text-slate-700">Correo electrónico</label>
                            <div class="mt-2 flex flex-col gap-3 sm:flex-row">
                                <input id="correo" type="email" wire:model="correo" placeholder="tucorreo@ejemplo.com"
                                    autocomplete="email"
                                    class="w-full flex-1 rounded-xl border border-stone-300 px-4 py-2.5 text-sm focus-visible:outline-2 focus-visible:outline-green-700">
                                <button type="submit" wire:loading.attr="disabled"
                                    class="inline-flex cursor-pointer items-center justify-center gap-2 rounded-full bg-green-800 px-6 py-2.5 text-sm font-semibold text-white hover:bg-green-900 focus-visible:outline-2 focus-visible:outline-green-700 disabled:opacity-60">
                                    <span wire:loading.remove wire:target="continuarConCorreo">Continuar</span>
                                    <span wire:loading wire:target="continuarConCorreo">Buscando…</span>
                                </button>
                            </div>
                            @error('correo')
                                <p class="mt-2 text-sm text-red-700">{{ $message }}</p>
                            @enderror
                            <p class="mt-2 text-xs text-slate-500">Lo usaremos para enviarte los detalles de tu pedido.</p>
                        </form>
                    @else
                        <div class="mt-4 flex flex-wrap items-center justify-between gap-3 rounded-xl bg-stone-50 px-4 py-3">
                            <p class="text-sm text-slate-700">
                                <span class="font-semibold text-slate-900">{{ $correo }}</span>
                            </p>
                            <button type="button" wire:click="editarCorreo"
                                class="cursor-pointer rounded-full px-3 py-1.5 text-sm font-semibold text-green-800 hover:bg-green-100 hover:underline focus-visible:outline-2 focus-visible:outline-green-700">
                                Cambiar
                            </button>
                        </div>
                    @endif
                </div>

                {{-- Paso 2: dirección --}}
                @if ($correoConfirmado)
                    <div class="rounded-2xl border border-stone-200 bg-white p-5">
                        <div class="flex items-center gap-3">
                            <span class="grid size-8 shrink-0 place-items-center rounded-full text-sm font-bold {{ $this->direccionLista ? 'bg-green-800 text-white' : 'bg-green-100 text-green-900' }}">2</span>
                            <h2 class="text-lg font-bold text-slate-900">Dirección de envío</h2>
                        </div>

                        @if ($direcciones->isNotEmpty() && ! $mostrarFormularioDireccion)
                            <ul class="mt-4 grid gap-3 sm:grid-cols-2">
                                @foreach ($direcciones as $dir)
                                    <li wire:key="dir-{{ $dir->id }}"
                                        class="rounded-xl border p-4 {{ $direccionSeleccionadaId === $dir->id ? 'border-green-700 bg-green-50' : 'border-stone-200' }}">
                                        <p class="text-sm font-semibold text-slate-900">{{ $dir->direccion }}, {{ $dir->colonia }}</p>
                                        <p class="mt-1 text-sm text-slate-600">{{ $dir->ciudad }}, {{ $dir->estado }} · C.P. {{ $dir->codigo_postal }}</p>
                                        @if ($dir->referencia)
                                            <p class="mt-1 text-xs text-slate-500">Ref: {{ $dir->referencia }}</p>
                                        @endif
                                        @if ($dir->telefono)
                                            <p class="mt-1 text-xs text-slate-500">Tel: {{ $dir->telefono }}</p>
                                        @endif
                                        <button type="button" wire:click="elegirDireccion({{ $dir->id }})"
                                            class="mt-3 inline-flex w-full cursor-pointer items-center justify-center rounded-full px-4 py-2 text-sm font-semibold focus-visible:outline-2 focus-visible:outline-green-700 {{ $direccionSeleccionadaId === $dir->id ? 'bg-green-800 text-white' : 'bg-green-100 text-green-900 hover:bg-green-200' }}">
                                            {{ $direccionSeleccionadaId === $dir->id ? 'Seleccionada' : 'Enviar aquí' }}
                                        </button>
                                    </li>
                                @endforeach
                            </ul>

                            <button type="button" wire:click="mostrarFormularioNuevaDireccion"
                                class="mt-4 inline-flex cursor-pointer items-center gap-2 rounded-full border border-stone-300 px-5 py-2 text-sm font-semibold text-slate-700 hover:bg-stone-50 focus-visible:outline-2 focus-visible:outline-green-700">
                                <i class="fa-solid fa-plus" aria-hidden="true"></i>
                                Agregar nueva dirección
                            </button>
                        @endif

                        @if ($mostrarFormularioDireccion)
                            <div class="mt-4 grid gap-3 sm:grid-cols-2">
                                <div class="sm:col-span-2">
                                    <label for="direccion" class="text-sm font-semibold text-slate-700">Calle y número *</label>
                                    <input id="direccion" type="text" wire:model.live="direccion" placeholder="Av. Juárez 123"
                                        class="mt-1 w-full rounded-xl border border-stone-300 px-4 py-2.5 text-sm focus-visible:outline-2 focus-visible:outline-green-700">
                                    @error('direccion') <p class="mt-1 text-sm text-red-700">{{ $message }}</p> @enderror
                                </div>
                                <div>
                                    <label for="colonia" class="text-sm font-semibold text-slate-700">Colonia *</label>
                                    <input id="colonia" type="text" wire:model.live="colonia"
                                        class="mt-1 w-full rounded-xl border border-stone-300 px-4 py-2.5 text-sm focus-visible:outline-2 focus-visible:outline-green-700">
                                    @error('colonia') <p class="mt-1 text-sm text-red-700">{{ $message }}</p> @enderror
                                </div>
                                <div>
                                    <label for="codigo_postal" class="text-sm font-semibold text-slate-700">Código postal *</label>
                                    <input id="codigo_postal" type="text" wire:model.live="codigo_postal" inputmode="numeric"
                                        class="mt-1 w-full rounded-xl border border-stone-300 px-4 py-2.5 text-sm focus-visible:outline-2 focus-visible:outline-green-700">
                                    @error('codigo_postal') <p class="mt-1 text-sm text-red-700">{{ $message }}</p> @enderror
                                </div>
                                <div>
                                    <label for="ciudad" class="text-sm font-semibold text-slate-700">Ciudad *</label>
                                    <input id="ciudad" type="text" wire:model.live="ciudad"
                                        class="mt-1 w-full rounded-xl border border-stone-300 px-4 py-2.5 text-sm focus-visible:outline-2 focus-visible:outline-green-700">
                                    @error('ciudad') <p class="mt-1 text-sm text-red-700">{{ $message }}</p> @enderror
                                </div>
                                <div>
                                    <label for="estado" class="text-sm font-semibold text-slate-700">Estado *</label>
                                    <input id="estado" type="text" wire:model.live="estado"
                                        class="mt-1 w-full rounded-xl border border-stone-300 px-4 py-2.5 text-sm focus-visible:outline-2 focus-visible:outline-green-700">
                                    @error('estado') <p class="mt-1 text-sm text-red-700">{{ $message }}</p> @enderror
                                </div>
                                <div>
                                    <label for="telefono" class="text-sm font-semibold text-slate-700">Teléfono *</label>
                                    <input id="telefono" type="tel" wire:model.live="telefono" autocomplete="tel"
                                        class="mt-1 w-full rounded-xl border border-stone-300 px-4 py-2.5 text-sm focus-visible:outline-2 focus-visible:outline-green-700">
                                    @error('telefono') <p class="mt-1 text-sm text-red-700">{{ $message }}</p> @enderror
                                </div>
                                <div>
                                    <label for="referencia" class="text-sm font-semibold text-slate-700">Referencia <span class="font-normal text-slate-500">(opcional)</span></label>
                                    <input id="referencia" type="text" wire:model.live="referencia" placeholder="Casa color azul, entre…"
                                        class="mt-1 w-full rounded-xl border border-stone-300 px-4 py-2.5 text-sm focus-visible:outline-2 focus-visible:outline-green-700">
                                    @error('referencia') <p class="mt-1 text-sm text-red-700">{{ $message }}</p> @enderror
                                </div>
                                <label class="flex cursor-pointer items-center gap-2 text-sm text-slate-700 sm:col-span-2">
                                    <input type="checkbox" wire:model.live="guardarDireccion"
                                        class="size-4 rounded border-stone-300 accent-green-800">
                                    Guardar dirección para futuros pedidos
                                </label>
                                @if ($direcciones->isNotEmpty())
                                    <div class="sm:col-span-2">
                                        <button type="button" wire:click="cancelarNuevaDireccion"
                                            class="cursor-pointer text-sm font-semibold text-slate-500 hover:underline focus-visible:outline-2 focus-visible:outline-green-700">
                                            Cancelar y elegir una guardada
                                        </button>
                                    </div>
                                @endif
                            </div>
                            @if (! $this->direccionLista)
                                <p class="mt-3 text-xs text-slate-500">Completa todos los campos marcados con * para continuar al pago con tarjeta.</p>
                            @endif
                        @endif
                    </div>
                @endif

                {{-- Paso 3: tarjeta --}}
                @if ($correoConfirmado)
                    <div class="rounded-2xl border border-stone-200 bg-white p-5">
                        <div class="flex items-center gap-3">
                            <span class="grid size-8 shrink-0 place-items-center rounded-full text-sm font-bold {{ $this->tarjetaLista && $this->direccionLista ? 'bg-green-800 text-white' : 'bg-green-100 text-green-900' }}">3</span>
                            <h2 class="text-lg font-bold text-slate-900">Tarjeta</h2>
                            <span class="ml-auto rounded-full bg-amber-100 px-3 py-1 text-xs font-semibold text-amber-900">Pago ficticio</span>
                        </div>

                        @if (! $this->direccionLista)
                            <p class="mt-3 rounded-xl bg-stone-50 px-4 py-3 text-sm text-slate-600">
                                @if ($direcciones->isNotEmpty() && ! $mostrarFormularioDireccion && $direccionSeleccionadaId === null)
                                    Elige una dirección con “Enviar aquí” para activar el pago con tarjeta.
                                @else
                                    Completa tu dirección de envío para activar el pago con tarjeta.
                                @endif
                            </p>
                        @endif

                        <div class="mt-4 grid gap-3 sm:grid-cols-2">
                            <div class="sm:col-span-2">
                                <label for="tarjeta_nombre" class="text-sm font-semibold text-slate-700">Nombre en la tarjeta *</label>
                                <input id="tarjeta_nombre" type="text" wire:model.live="tarjeta_nombre" autocomplete="cc-name" placeholder="Como aparece en la tarjeta"
                                    @disabled(! $this->direccionLista)
                                    class="mt-1 w-full rounded-xl border border-stone-300 px-4 py-2.5 text-sm focus-visible:outline-2 focus-visible:outline-green-700 disabled:cursor-not-allowed disabled:bg-stone-100 disabled:text-slate-400">
                                @error('tarjeta_nombre') <p class="mt-1 text-sm text-red-700">{{ $message }}</p> @enderror
                            </div>
                            <div class="sm:col-span-2">
                                <label for="tarjeta_numero" class="text-sm font-semibold text-slate-700">Número de tarjeta *</label>
                                <input id="tarjeta_numero" type="text" wire:model.live="tarjeta_numero" inputmode="numeric" autocomplete="cc-number" placeholder="4111 1111 1111 1111"
                                    @disabled(! $this->direccionLista)
                                    class="mt-1 w-full rounded-xl border border-stone-300 px-4 py-2.5 text-sm tabular-nums focus-visible:outline-2 focus-visible:outline-green-700 disabled:cursor-not-allowed disabled:bg-stone-100 disabled:text-slate-400">
                                @error('tarjeta_numero') <p class="mt-1 text-sm text-red-700">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label for="tarjeta_vencimiento" class="text-sm font-semibold text-slate-700">Vencimiento (mm/yy) *</label>
                                <input id="tarjeta_vencimiento" type="text" wire:model.live="tarjeta_vencimiento" inputmode="numeric" autocomplete="cc-exp" placeholder="12/28"
                                    @disabled(! $this->direccionLista)
                                    class="mt-1 w-full rounded-xl border border-stone-300 px-4 py-2.5 text-sm tabular-nums focus-visible:outline-2 focus-visible:outline-green-700 disabled:cursor-not-allowed disabled:bg-stone-100 disabled:text-slate-400">
                                @error('tarjeta_vencimiento') <p class="mt-1 text-sm text-red-700">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label for="tarjeta_cvv" class="text-sm font-semibold text-slate-700">CVV *</label>
                                <input id="tarjeta_cvv" type="password" wire:model.live="tarjeta_cvv" inputmode="numeric" autocomplete="cc-csc" placeholder="123"
                                    @disabled(! $this->direccionLista)
                                    class="mt-1 w-full rounded-xl border border-stone-300 px-4 py-2.5 text-sm tabular-nums focus-visible:outline-2 focus-visible:outline-green-700 disabled:cursor-not-allowed disabled:bg-stone-100 disabled:text-slate-400">
                                @error('tarjeta_cvv') <p class="mt-1 text-sm text-red-700">{{ $message }}</p> @enderror
                            </div>
                        </div>

                        @if ($pagoError)
                            <p role="alert" class="mt-3 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm font-semibold text-red-800">
                                {{ $pagoError }}
                            </p>
                        @endif
                    </div>
                @endif
            </div>

            {{-- Sidebar derecho: resumen --}}
            <aside aria-label="Resumen del pedido" class="lg:col-span-1">
                <div class="rounded-2xl border border-stone-200 bg-stone-50 p-5 lg:sticky lg:top-4">
                    <h2 class="text-lg font-bold text-slate-900">Resumen</h2>

                    <ul class="mt-4 max-h-64 space-y-3 overflow-y-auto">
                        @foreach ($items as $item)
                            <li wire:key="checkout-{{ $item['producto']->id }}" class="flex items-center gap-3">
                                <span class="grid size-11 shrink-0 place-items-center overflow-hidden rounded-lg bg-white">
                                    <img src="{{ $item['producto']->imagen ? asset('storage/' . $item['producto']->imagen) : asset('images/products/placeholder.jpg') }}"
                                        alt="" loading="lazy" class="size-full object-contain">
                                </span>
                                <span class="min-w-0 flex-1">
                                    <span class="block truncate text-sm font-semibold text-slate-900">{{ $item['producto']->nombre }}</span>
                                    <span class="block text-xs text-slate-600 tabular-nums">{{ $item['cantidad'] }} × ${{ number_format($item['producto']->precio_mxn, 2) }}</span>
                                </span>
                                <span class="text-sm font-semibold text-slate-900 tabular-nums">${{ number_format($item['linea'], 2) }}</span>
                            </li>
                        @endforeach
                    </ul>

                    <dl class="mt-4 space-y-2 border-t border-stone-200 pt-4 text-sm tabular-nums">
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

                    <button type="button" wire:click="procesarPedido" wire:loading.attr="disabled"
                        @disabled(! $this->puedeProcesar)
                        title="{{ $this->puedeProcesar ? 'Procesar pedido' : 'Completa correo, dirección y tarjeta para continuar' }}"
                        class="mt-4 flex w-full items-center justify-center gap-2 rounded-full px-6 py-2.5 text-sm font-semibold focus-visible:outline-2 focus-visible:outline-green-700 {{ $this->puedeProcesar ? 'cursor-pointer bg-green-800 text-white hover:bg-green-900' : 'cursor-not-allowed bg-stone-200 text-slate-500' }}">
                        <span wire:loading.remove wire:target="procesarPedido">
                            <i class="fa-solid fa-lock" aria-hidden="true"></i>
                            Procesar pedido
                        </span>
                        <span wire:loading wire:target="procesarPedido">Procesando pago…</span>
                    </button>

                    @if (! $this->puedeProcesar)
                        <p class="mt-2 text-center text-xs text-slate-500">Completa correo, dirección y tarjeta para habilitar el pago.</p>
                    @endif

                    <a href="{{ route('home.carrito') }}" wire:navigate
                        class="mt-2 inline-flex w-full items-center justify-center gap-2 rounded-full px-6 py-2.5 text-sm font-semibold text-green-800 hover:bg-green-50 hover:underline focus-visible:outline-2 focus-visible:outline-green-700">
                        Volver al carrito
                    </a>
                </div>
            </aside>
        </div>
    @endif
</section>
