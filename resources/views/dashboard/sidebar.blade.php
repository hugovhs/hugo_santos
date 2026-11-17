<aside class="fixed inset-y-0 left-0 z-40 flex w-64 -translate-x-full flex-col bg-slate-900 text-slate-300 transition-transform duration-200 ease-in-out peer-checked:translate-x-0 lg:translate-x-0">
    <div class="flex h-16 shrink-0 items-center justify-between border-b border-slate-800 px-6">
        <a href="{{ route('dashboard') }}" class="flex items-center gap-2 text-lg font-semibold text-white">
            <i class="fa-solid fa-user-shield"></i>
            Admin
        </a>

        {{-- Botón cerrar (móvil) --}}
        <label for="sidebar-toggle" class="cursor-pointer rounded-md p-1 text-slate-400 hover:bg-slate-800 hover:text-white lg:hidden">
            <i class="fa-solid fa-xmark"></i>
        </label>
    </div>

    <nav class="flex-1 space-y-1 overflow-y-auto px-3 py-4">
        <a href="{{ route('dashboard') }}"
           class="flex items-center gap-3 rounded-lg px-3 py-2 text-sm font-medium transition-colors {{ request()->routeIs('dashboard') ? 'bg-indigo-600 text-white' : 'hover:bg-slate-800 hover:text-white' }}">
            <i class="fa-solid fa-house"></i>
            Dashboard
        </a>

        <a href="{{ route('dashboard.productos') }}"
           class="flex items-center gap-3 rounded-lg px-3 py-2 text-sm font-medium transition-colors {{ request()->is('dashboard.productos') ? 'bg-indigo-600 text-white' : 'hover:bg-slate-800 hover:text-white' }}">
            <i class="fa-solid fa-box"></i>
            Productos
        </a>
    </nav>

    {{-- Datos de usuario loggeado --}}
    <div class="shrink-0 border-t border-slate-800 p-4">
        <div class="flex items-center gap-3">
            <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-indigo-600 text-sm font-semibold text-white">
                {{ strtoupper(substr(auth()->user()->name ?? 'A', 0, 1)) }}
            </div>

            <div class="min-w-0">
                <p class="truncate text-sm font-medium text-white">{{ auth()->user()->name ?? 'Administrador' }}</p>
                <p class="truncate text-xs text-slate-400">{{ auth()->user()->email ?? '' }}</p>
            </div>
        </div>
    </div>
</aside>