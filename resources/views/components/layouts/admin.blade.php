<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">

        <title>{{ $title ?? 'Page Title' }}</title>

        @vite('resources/css/app.css')
    </head>
    
    <body class="min-h-screen bg-slate-100 text-slate-900 antialiased">
        {{-- Toggle del sidebar en móvil --}}
        <input type="checkbox" id="sidebar-toggle" class="peer hidden">

        {{-- Overlay para cerrar el sidebar en móvil --}}
        <label for="sidebar-toggle" class="fixed inset-0 z-30 hidden bg-slate-900/60 peer-checked:block lg:hidden" aria-hidden="true"></label>

        {{-- Sidebar --}}
        @include('dashboard.sidebar')

        {{-- Main content --}}
        <div class="flex min-h-screen flex-col lg:pl-64">
            <header class="sticky top-0 z-20 flex h-16 shrink-0 items-center gap-4 border-b border-slate-200 bg-white px-4 shadow-sm sm:px-6">
                {{-- Botón abrir sidebar (móvil) --}}
                <label for="sidebar-toggle" class="cursor-pointer rounded-md p-2 text-slate-500 hover:bg-slate-100 hover:text-slate-900 lg:hidden">
                    <i class="fa-solid fa-bars"></i>
                </label>

                <h1 class="truncate text-lg font-semibold text-slate-900">{{ $title ?? 'Dashboard' }}</h1>

                <div class="ml-auto flex items-center gap-3">
                    <span class="hidden text-sm text-slate-500 sm:inline">{{ now()->translatedFormat('d M Y') }}</span>
                    
                    <div class="flex h-9 w-9 items-center justify-center rounded-full bg-indigo-600 text-sm font-semibold text-white">
                        {{ strtoupper(substr(auth()->user()->name ?? 'A', 0, 1)) }}
                    </div>
                </div>
            </header>

            {{-- Page content --}}
            <main class="flex-1 p-4 sm:p-6 lg:p-8">
                {{ $slot }}
            </main>

            <footer class="shrink-0 border-t border-slate-200 bg-white px-4 py-4 sm:px-6">
                <div class="flex flex-col items-center justify-between gap-2 text-sm text-slate-500 sm:flex-row">
                    <p>&copy; {{ date('Y') }} {{ config('app.name', 'Laravel') }}. Todos los derechos reservados.</p>
                    <p>v0.0.1</p>
                </div>
            </footer>
        </div>
    </body>
</html>
