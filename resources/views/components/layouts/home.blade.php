<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">

        <title>{{ $title ?? 'Inicio' }} | NutriViva</title>

        @vite('resources/css/app.css')
        @livewireStyles
    </head>

    <body class="flex min-h-screen flex-col bg-white text-slate-900 antialiased">
        <header class="border-b border-stone-200">
            <nav aria-label="Navbar"
                class="mx-auto grid max-w-7xl grid-cols-[1fr_auto] items-center gap-x-2 gap-y-3 px-4 py-4 sm:grid-cols-[auto_1fr_auto] sm:gap-x-6 sm:px-8 lg:grid-cols-[auto_auto_1fr_auto]">
                <a href="{{ route('home.index') }}" class="rounded focus-visible:outline-2 focus-visible:outline-offset-4 focus-visible:outline-green-700">
                    <img src="{{ asset('images/logo_horizontal.webp') }}" width="2172" height="724"
                        alt="NutriViva · Inicio" class="h-auto w-24 sm:w-44">
                </a>

                <div class="order-2 col-span-full flex items-center gap-1 sm:col-span-1 lg:ml-3">
                    <a href="{{ route('home.index') }}" @if (request()->routeIs('home.index')) aria-current="page" @endif
                        class="rounded-lg px-2 py-2.5 text-sm font-semibold text-slate-700 hover:bg-green-50 hover:text-green-800 focus-visible:outline-2 focus-visible:outline-green-700 aria-[current=page]:bg-green-50 aria-[current=page]:text-green-800 sm:px-3">
                        Inicio
                    </a>

                    <div class="relative" x-data="{ abierto: false }"
                        @keydown.escape="if (abierto) { abierto = false; $refs.boton.focus() }">
                        <button type="button" x-ref="boton" @click="abierto = !abierto"
                            aria-controls="categorias-nav" :aria-expanded="abierto.toString()"
                            class="inline-flex items-center gap-2 rounded-lg px-2 py-2.5 text-sm font-semibold text-slate-700 hover:bg-green-50 hover:text-green-800 focus-visible:outline-2 focus-visible:outline-green-700 sm:px-3">
                            Categorías
                            <i class="fa-solid fa-chevron-down text-[0.625rem]" aria-hidden="true"
                                :class="{ 'rotate-180': abierto }"></i>
                        </button>

                        <ul id="categorias-nav" x-show="abierto" @click.outside="abierto = false" style="display: none;"
                            class="absolute left-0 z-20 mt-2 max-h-72 w-44 overflow-y-auto rounded-xl border border-stone-200 bg-white p-2 shadow-lg sm:w-56">
                            @forelse ($categorias as $categoria)
                                <li>
                                    <a href="{{ route('home.productos', ['categoria' => $categoria->id]) }}" wire:navigate
                                        @click="abierto = false"
                                        class="block rounded-lg px-3 py-2 text-sm break-words text-slate-700 hover:bg-green-50 hover:text-green-800 focus-visible:outline-2 focus-visible:outline-green-700">
                                        {{ $categoria->nombre }}
                                    </a>
                                </li>
                            @empty
                                <li class="px-3 py-2 text-sm text-slate-600">Aún no hay categorías.</li>
                            @endforelse
                        </ul>
                    </div>
                </div>

                <form method="get" action="{{ route('home.productos') }}" role="search"
                    class="relative order-4 col-span-full w-full lg:order-3 lg:col-span-1 lg:max-w-sm lg:justify-self-end">
                    <label for="buscar-productos" class="sr-only">Buscar productos</label>
                    <input id="buscar-productos" type="search" name="q" placeholder="Buscar por nombre o SKU"
                        class="w-full rounded-full border border-stone-200 bg-stone-50 py-2.5 pr-12 pl-4 text-sm placeholder:text-slate-600 focus-visible:outline-2 focus-visible:outline-green-700">
                    <button type="submit" aria-label="Buscar"
                        class="absolute inset-y-1 right-1 grid w-9 cursor-pointer place-items-center rounded-full text-sm text-green-800 hover:bg-green-100 focus-visible:outline-2 focus-visible:outline-green-700">
                        <i class="fa-solid fa-magnifying-glass" aria-hidden="true"></i>
                    </button>
                </form>

                <button type="button" disabled aria-label="Carrito"
                    class="order-1 inline-flex items-center gap-2 justify-self-end rounded-full bg-green-50 px-3 py-2.5 text-green-800 sm:order-3 sm:px-4 lg:order-4">
                    <i class="fa-solid fa-cart-shopping text-sm" aria-hidden="true"></i>
                    <span class="min-w-5 rounded-full bg-green-800 px-1 text-center text-xs leading-5 font-semibold text-white" aria-hidden="true">0</span>
                </button>
            </nav>
        </header>

        <main class="flex-1">
            {{ $slot }}
        </main>

        <footer class="border-t border-stone-200 bg-stone-50">
            <div class="mx-auto flex max-w-7xl flex-wrap items-center justify-between gap-x-6 gap-y-2 px-4 py-6 sm:px-8">
                <a href="{{ route('home.index') }}" class="rounded font-semibold text-green-800 focus-visible:outline-2 focus-visible:outline-offset-4 focus-visible:outline-green-700">NutriViva</a>
                <p class="text-sm text-slate-600">© {{ now()->year }} NutriViva. Todos los derechos reservados.</p>
            </div>
        </footer>

        @livewireScripts
    </body>
</html>
