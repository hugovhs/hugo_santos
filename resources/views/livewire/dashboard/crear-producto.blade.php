<div>
    <div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-xl font-semibold text-slate-900">Agregar producto</h1>
            <p class="mt-1 text-sm text-slate-500">Registra un nuevo producto en el catálogo.</p>
        </div>

        {{-- Muestra si hay cambios sin guardar --}}
        <a
            href="{{ route('dashboard.productos') }}"
            x-data
            @click.prevent="
                const sucio = $wire.sku || $wire.nombre || $wire.precio_usd || $wire.precio_mxn || $wire.imagen;
                if (!sucio || confirm('¿Descartar los cambios del producto?')) {
                    window.location.href = $el.href;
                }
            "
            class="inline-flex items-center gap-2 rounded-md border border-slate-300 bg-white px-4 py-2 text-sm font-semibold text-slate-700 shadow-sm transition hover:bg-slate-50"
        >
            <i class="fa-solid fa-arrow-left" aria-hidden="true"></i>
            Volver a productos
        </a>
    </div>

    <form wire:submit="guardar" novalidate>
        <div class="rounded-lg border border-slate-200 bg-white p-6 shadow-sm">
            {{-- Información del tipo de cambio --}}
            @if ($tipoCambio)
                <div class="mb-6 flex items-start gap-2 rounded-md border border-indigo-100 bg-indigo-50 px-4 py-3 text-sm text-indigo-700">
                    <i class="fa-solid fa-money-bill-transfer mt-0.5" aria-hidden="true"></i>
                    <span>
                        Tipo de cambio: <strong>1 USD = ${{ number_format($tipoCambio, 4) }} MXN</strong>.
                    </span>
                </div>
            @elseif ($errorTipoCambio)
                <div class="mb-6 flex flex-col gap-2 rounded-md border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-800 sm:flex-row sm:items-center sm:justify-between">
                    <span>
                        <i class="fa-solid fa-triangle-exclamation mr-1" aria-hidden="true"></i>
                        No se pudo obtener el tipo de cambio. Captura ambos precios manualmente.
                    </span>
                    <button
                        type="button"
                        wire:click="reintentarTipoCambio"
                        wire:loading.attr="disabled"
                        wire:target="reintentarTipoCambio"
                        class="inline-flex shrink-0 items-center gap-1 rounded-md border border-amber-300 bg-white px-3 py-1.5 text-xs font-semibold text-amber-800 transition hover:bg-amber-100 disabled:opacity-60"
                    >
                        <i class="fa-solid fa-rotate-right" aria-hidden="true"></i>
                        Reintentar
                    </button>
                </div>
            @endif

            <div class="grid gap-5 sm:grid-cols-2">
                {{-- SKU --}}
                <div>
                    <label for="sku" class="block text-sm font-medium text-slate-700">
                        SKU <span class="text-rose-500">*</span>
                    </label>
                    <input
                        id="sku"
                        type="text"
                        wire:model.live.debounce.400ms="sku"
                        placeholder="p. ej. PROD-001"
                        autocomplete="off"
                        class="mt-1 block w-full rounded-md border px-3 py-2 text-sm shadow-sm transition focus:outline-none focus:ring-2 {{ $errors->has('sku') ? 'border-rose-400 focus:border-rose-500 focus:ring-rose-100' : 'border-slate-300 focus:border-indigo-500 focus:ring-indigo-100' }}"
                    >
                    @error('sku')
                        <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Nombre --}}
                <div>
                    <label for="nombre" class="block text-sm font-medium text-slate-700">
                        Nombre <span class="text-rose-500">*</span>
                    </label>
                    <input
                        id="nombre"
                        type="text"
                        wire:model.live.debounce.400ms="nombre"
                        oninput="this.value = this.value.replace(/[áàäâãå]/g, 'a').replace(/[ÁÀÄÂÃÅ]/g, 'A').replace(/[éèëê]/g, 'e').replace(/[ÉÈËÊ]/g, 'E').replace(/[íìïî]/g, 'i').replace(/[ÍÌÏÎ]/g, 'I').replace(/[óòöôõ]/g, 'o').replace(/[ÓÒÖÔÕ]/g, 'O').replace(/[úùüû]/g, 'u').replace(/[ÚÙÜÛ]/g, 'U').replace(/ñ/g, 'n').replace(/Ñ/g, 'N').replace(/ç/g, 'c').replace(/Ç/g, 'C').replace(/[^A-Za-z0-9 ]/g, '')"
                        placeholder="Nombre del producto"
                        autocomplete="off"
                        class="mt-1 block w-full rounded-md border px-3 py-2 text-sm shadow-sm transition focus:outline-none focus:ring-2 {{ $errors->has('nombre') ? 'border-rose-400 focus:border-rose-500 focus:ring-rose-100' : 'border-slate-300 focus:border-indigo-500 focus:ring-indigo-100' }}"
                    >
                    <p class="mt-1 text-xs text-slate-400">Sin acentos ni caracteres especiales.</p>
                    @error('nombre')
                        <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Precio USD --}}
                <div>
                    <label for="precio_usd" class="block text-sm font-medium text-slate-700">
                        Precio USD <span class="text-rose-500">*</span>
                    </label>
                    <div class="relative mt-1">
                        <span class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3 text-sm text-slate-400">$</span>
                        <input
                            id="precio_usd"
                            type="number"
                            step="0.01"
                            min="0.01"
                            inputmode="decimal"
                            wire:model.live.debounce.400ms="precio_usd"
                            placeholder="0.00"
                            class="block w-full rounded-md border py-2 pl-7 pr-3 text-sm shadow-sm transition focus:outline-none focus:ring-2 {{ $errors->has('precio_usd') ? 'border-rose-400 focus:border-rose-500 focus:ring-rose-100' : 'border-slate-300 focus:border-indigo-500 focus:ring-indigo-100' }}"
                        >
                    </div>
                    @error('precio_usd')
                        <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Precio MXN --}}
                <div>
                    <label for="precio_mxn" class="block text-sm font-medium text-slate-700">
                        Precio MXN <span class="text-rose-500">*</span>
                    </label>
                    <div class="relative mt-1">
                        <span class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3 text-sm text-slate-400">$</span>
                        <input
                            id="precio_mxn"
                            type="number"
                            step="0.01"
                            min="0.01"
                            inputmode="decimal"
                            wire:model.live.debounce.400ms="precio_mxn"
                            placeholder="0.00"
                            class="block w-full rounded-md border py-2 pl-7 pr-3 text-sm shadow-sm transition focus:outline-none focus:ring-2 {{ $errors->has('precio_mxn') ? 'border-rose-400 focus:border-rose-500 focus:ring-rose-100' : 'border-slate-300 focus:border-indigo-500 focus:ring-indigo-100' }}"
                        >
                    </div>
                    @error('precio_mxn')
                        <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Stock --}}
                <div>
                    <label for="stock" class="block text-sm font-medium text-slate-700">
                        Stock <span class="text-rose-500">*</span>
                    </label>
                    <input
                        id="stock"
                        type="number"
                        step="1"
                        min="0"
                        inputmode="numeric"
                        wire:model.live.debounce.400ms="stock"
                        class="mt-1 block w-full rounded-md border px-3 py-2 text-sm shadow-sm transition focus:outline-none focus:ring-2 {{ $errors->has('stock') ? 'border-rose-400 focus:border-rose-500 focus:ring-rose-100' : 'border-slate-300 focus:border-indigo-500 focus:ring-indigo-100' }}"
                    >
                    @error('stock')
                        <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Fecha de vigencia --}}
                <div>
                    <label for="fecha_vigencia" class="block text-sm font-medium text-slate-700">Fecha de vigencia</label>
                    <input
                        id="fecha_vigencia"
                        type="date"
                        wire:model.live="fecha_vigencia"
                        class="mt-1 block w-full rounded-md border px-3 py-2 text-sm shadow-sm transition focus:outline-none focus:ring-2 {{ $errors->has('fecha_vigencia') ? 'border-rose-400 focus:border-rose-500 focus:ring-rose-100' : 'border-slate-300 focus:border-indigo-500 focus:ring-indigo-100' }}"
                    >
                    @error('fecha_vigencia')
                        <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Descripción corta --}}
                <div class="sm:col-span-2" x-data>
                    <div class="flex items-center justify-between">
                        <label for="descripcion_corta" class="block text-sm font-medium text-slate-700">Descripción corta</label>
                        <span class="text-xs text-slate-400">
                            <span x-text="($wire.descripcion_corta ?? '').length">0</span>/500
                        </span>
                    </div>
                    <input
                        id="descripcion_corta"
                        type="text"
                        maxlength="500"
                        wire:model.live.debounce.400ms="descripcion_corta"
                        placeholder="Resumen breve del producto"
                        class="mt-1 block w-full rounded-md border px-3 py-2 text-sm shadow-sm transition focus:outline-none focus:ring-2 {{ $errors->has('descripcion_corta') ? 'border-rose-400 focus:border-rose-500 focus:ring-rose-100' : 'border-slate-300 focus:border-indigo-500 focus:ring-indigo-100' }}"
                    >
                    @error('descripcion_corta')
                        <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Descripción larga --}}
                <div class="sm:col-span-2" x-data>
                    <div class="flex items-center justify-between">
                        <label for="descripcion_larga" class="block text-sm font-medium text-slate-700">Descripción larga</label>
                        <span class="text-xs text-slate-400">
                            <span x-text="($wire.descripcion_larga ?? '').length">0</span>/2000
                        </span>
                    </div>
                    <textarea
                        id="descripcion_larga"
                        rows="4"
                        maxlength="2000"
                        wire:model.live.debounce.600ms="descripcion_larga"
                        placeholder="Descripción detallada del producto"
                        class="mt-1 block w-full rounded-md border px-3 py-2 text-sm shadow-sm transition focus:outline-none focus:ring-2 {{ $errors->has('descripcion_larga') ? 'border-rose-400 focus:border-rose-500 focus:ring-rose-100' : 'border-slate-300 focus:border-indigo-500 focus:ring-indigo-100' }}"
                    ></textarea>
                    @error('descripcion_larga')
                        <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Imagen del producto --}}
                <div class="sm:col-span-2">
                    <label for="imagen" class="block text-sm font-medium text-slate-700">Imagen del producto</label>
                    <input
                        id="imagen"
                        type="file"
                        wire:model="imagen"
                        accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp"
                        class="mt-1 block w-full cursor-pointer rounded-md border border-slate-300 bg-white text-sm text-slate-500 shadow-sm transition file:mr-4 file:cursor-pointer file:rounded-l-md file:border-0 file:bg-indigo-50 file:px-4 file:py-2 file:text-sm file:font-semibold file:text-indigo-700 hover:file:bg-indigo-100 focus:outline-none focus:ring-2 {{ $errors->has('imagen') ? 'border-rose-400 focus:border-rose-500 focus:ring-rose-100' : 'focus:border-indigo-500 focus:ring-indigo-100' }}"
                    >
                    <p class="mt-1 text-xs text-slate-400">Formatos permitidos: JPG, JPEG, PNG o WebP. Máximo 2 MB.</p>
                    @error('imagen')
                        <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
                    @enderror

                    {{-- spinner --}}
                    <div wire:loading wire:target="imagen" class="mt-2 flex items-center gap-2 text-xs text-indigo-600">
                        <i class="fa-solid fa-spinner fa-spin" aria-hidden="true"></i>
                        Subiendo imagen…
                    </div>

                    {{-- Vista previa de la imagen --}}
                    @if ($imagen && $imagen->isPreviewable())
                        <div class="mt-3 flex items-start gap-3">
                            <img
                                src="{{ $imagen->temporaryUrl() }}"
                                alt="Vista previa de la imagen del producto"
                                class="h-28 w-28 rounded-md border border-slate-200 object-cover shadow-sm"
                            >
                            <button
                                type="button"
                                wire:click="$set('imagen', null)"
                                class="inline-flex items-center gap-1 rounded-md border border-slate-300 bg-white px-3 py-1.5 text-xs font-semibold text-slate-600 transition hover:bg-rose-50 hover:text-rose-600"
                            >
                                <i class="fa-solid fa-trash-can" aria-hidden="true"></i>
                                Quitar imagen
                            </button>
                        </div>
                    @endif
                </div>

                {{-- Estatus --}}
                <div class="sm:col-span-2">
                    <label class="inline-flex cursor-pointer items-center gap-3">
                        <input
                            type="checkbox"
                            wire:model.live="activo"
                            class="h-4 w-4 rounded border-slate-300 text-indigo-600 focus:ring-indigo-500"
                        >
                        <span class="text-sm font-medium text-slate-700">Producto activo</span>
                    </label>
                    <p class="mt-1 text-xs text-slate-400">Los productos inactivos no se muestran en la tienda.</p>
                    @error('activo')
                        <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            {{-- Acciones --}}
            <div class="mt-6 flex items-center justify-end gap-3 border-t border-slate-200 pt-5">
                <a
                    href="{{ route('dashboard.productos') }}"
                    class="rounded-md border border-slate-300 bg-white px-4 py-2 text-sm font-semibold text-slate-700 shadow-sm transition hover:bg-slate-50"
                >
                    Cancelar
                </a>
                <button
                    type="submit"
                    wire:loading.attr="disabled"
                    wire:target="guardar"
                    class="inline-flex items-center gap-2 rounded-md bg-indigo-600 px-4 py-2 text-sm font-semibold text-white shadow-sm transition hover:bg-indigo-500 disabled:cursor-not-allowed disabled:opacity-60"
                >
                    <i class="fa-solid fa-floppy-disk" wire:loading.remove wire:target="guardar" aria-hidden="true"></i>
                    <i class="fa-solid fa-spinner fa-spin" wire:loading wire:target="guardar" aria-hidden="true"></i>
                    <span wire:loading.remove wire:target="guardar">Guardar producto</span>
                    <span wire:loading wire:target="guardar">Guardando…</span>
                </button>
            </div>
        </div>
    </form>
</div>
