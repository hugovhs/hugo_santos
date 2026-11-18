<div>
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
            <h1 class="text-xl font-semibold text-slate-900">Categorías</h1>
            <p class="mt-1 text-sm text-slate-500">Administrar categorías del catálogo.</p>
        </div>

        <div class="flex flex-col gap-3 sm:flex-row sm:items-center">
            <div class="relative">
                <i class="fa-solid fa-magnifying-glass pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 text-slate-400" aria-hidden="true"></i>
                <input
                    type="search"
                    wire:model.live.debounce.300ms="search"
                    placeholder="Buscar por nombre..."
                    aria-label="Buscar categorías por nombre"
                    class="w-full rounded-md border border-slate-300 bg-white py-2 pl-9 pr-3 text-sm text-slate-900 shadow-sm placeholder:text-slate-400 focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500 sm:w-72"
                />
            </div>

            <button
                type="button"
                wire:click="nueva"
                class="inline-flex items-center justify-center gap-2 rounded-md bg-indigo-600 px-4 py-2 text-sm font-semibold text-white shadow-sm transition hover:bg-indigo-500"
            >
                <i class="fa-solid fa-plus" aria-hidden="true"></i>
                Agregar categoría
            </button>
        </div>
    </div>

    @if ($categorias->count())
        <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
            @foreach ($categorias as $categoria)
                <article class="flex flex-col rounded-lg border border-slate-200 bg-white p-5 shadow-sm transition hover:shadow">
                    <h2 class="text-base font-semibold text-slate-900">{{ $categoria->nombre }}</h2>
                    <p class="mt-2 flex-1 text-sm text-slate-600">{{ $categoria->descripcion ?: 'Sin descripción.' }}</p>
                    <div class="mt-4 flex items-center justify-end border-t border-slate-100 pt-3">
                        <button
                            type="button"
                            wire:click="editar({{ $categoria->id }})"
                            class="inline-flex items-center gap-2 rounded-md border border-slate-300 bg-white px-3 py-1.5 text-xs font-semibold text-slate-700 shadow-sm transition hover:bg-indigo-50 hover:text-indigo-700"
                        >
                            <i class="fa-solid fa-pen" aria-hidden="true"></i>
                            Editar
                        </button>
                    </div>
                </article>
            @endforeach
        </div>

        <div class="mt-4">
            {{ $categorias->links() }}
        </div>
    @else
        <div class="rounded-lg border border-dashed border-slate-300 bg-white px-4 py-10 text-center text-sm text-slate-500">
            @if (trim($search) !== '')
                No se encontraron categorías para «{{ $search }}».
            @else
                No hay categorías registradas.
            @endif
        </div>
    @endif

    @if ($mostrarModal)
        <div
            class="fixed inset-0 z-50 flex items-center justify-center p-4"
            role="dialog"
            aria-modal="true"
            aria-label="{{ $editandoId ? 'Editar categoría' : 'Nueva categoría' }}"
        >
            <div class="absolute inset-0 bg-slate-900/60" wire:click="cerrarModal" aria-hidden="true"></div>

            <div class="relative w-full max-w-md rounded-lg bg-white shadow-xl">
                <div class="flex items-center justify-between border-b border-slate-200 px-5 py-4">
                    <h2 class="text-base font-semibold text-slate-900">
                        {{ $editandoId ? 'Editar categoría' : 'Nueva categoría' }}
                    </h2>
                    <button
                        type="button"
                        wire:click="cerrarModal"
                        class="rounded-md p-1.5 text-slate-400 transition hover:bg-slate-100 hover:text-slate-700"
                        aria-label="Cerrar"
                    >
                        <i class="fa-solid fa-xmark" aria-hidden="true"></i>
                    </button>
                </div>

                <form wire:submit="guardar" novalidate>
                    <div class="space-y-4 px-5 py-5">
                        <div>
                            <label for="categoria-nombre" class="block text-sm font-medium text-slate-700">
                                Nombre <span class="text-rose-500">*</span>
                            </label>
                            <input
                                id="categoria-nombre"
                                type="text"
                                wire:model.live.debounce.400ms="nombre"
                                oninput="this.value = this.value.replace(/[áàäâãå]/g, 'a').replace(/[ÁÀÄÂÃÅ]/g, 'A').replace(/[éèëê]/g, 'e').replace(/[ÉÈËÊ]/g, 'E').replace(/[íìïî]/g, 'i').replace(/[ÍÌÏÎ]/g, 'I').replace(/[óòöôõ]/g, 'o').replace(/[ÓÒÖÔÕ]/g, 'O').replace(/[úùüû]/g, 'u').replace(/[ÚÙÜÛ]/g, 'U').replace(/ñ/g, 'n').replace(/Ñ/g, 'N').replace(/ç/g, 'c').replace(/Ç/g, 'C').replace(/[^A-Za-z0-9 ]/g, '')"
                                placeholder="Nombre de la categoría"
                                autocomplete="off"
                                class="mt-1 block w-full rounded-md border px-3 py-2 text-sm shadow-sm transition focus:outline-none focus:ring-2 {{ $errors->has('nombre') ? 'border-rose-400 focus:border-rose-500 focus:ring-rose-100' : 'border-slate-300 focus:border-indigo-500 focus:ring-indigo-100' }}"
                            >
                            <p class="mt-1 text-xs text-slate-400">Sin acentos ni caracteres especiales.</p>
                            @error('nombre')
                                <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="categoria-descripcion" class="block text-sm font-medium text-slate-700">
                                Descripción
                            </label>
                            <textarea
                                id="categoria-descripcion"
                                rows="3"
                                maxlength="1000"
                                wire:model.live.debounce.400ms="descripcion"
                                placeholder="Descripción de la categoría"
                                class="mt-1 block w-full rounded-md border px-3 py-2 text-sm shadow-sm transition focus:outline-none focus:ring-2 {{ $errors->has('descripcion') ? 'border-rose-400 focus:border-rose-500 focus:ring-rose-100' : 'border-slate-300 focus:border-indigo-500 focus:ring-indigo-100' }}"
                            ></textarea>
                            @error('descripcion')
                                <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="flex items-center justify-end gap-3 border-t border-slate-200 px-5 py-4">
                        <button
                            type="button"
                            wire:click="cerrarModal"
                            class="rounded-md border border-slate-300 bg-white px-4 py-2 text-sm font-semibold text-slate-700 shadow-sm transition hover:bg-slate-50"
                        >
                            Cancelar
                        </button>
                        <button
                            type="submit"
                            wire:loading.attr="disabled"
                            wire:target="guardar"
                            class="inline-flex items-center gap-2 rounded-md bg-indigo-600 px-4 py-2 text-sm font-semibold text-white shadow-sm transition hover:bg-indigo-500 disabled:cursor-not-allowed disabled:opacity-60"
                        >
                            <i class="fa-solid fa-floppy-disk" wire:loading.remove wire:target="guardar" aria-hidden="true"></i>
                            <i class="fa-solid fa-spinner fa-spin" wire:loading wire:target="guardar" aria-hidden="true"></i>
                            <span wire:loading.remove wire:target="guardar">{{ $editandoId ? 'Guardar cambios' : 'Crear categoría' }}</span>
                            <span wire:loading wire:target="guardar">Guardando…</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>
