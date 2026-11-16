<div>
    <div class="flex justify-center items-center h-screen">
        <div class="w-full max-w-md border border-gray-300 rounded-md p-4">
            <div class="flex flex-col items-center mb-12 gap-2">
                <h3 class="text-2xl font-bold">Iniciar sesión</h3>
                <img src="{{ asset('images/logo_vertical.webp') }}" alt="Logo" class="w-1/2">
            </div>

            <hr class="w-full border-gray-300 my-4">
            
            <div class="flex flex-col items-center gap-2">
                <form wire:submit="login">
                    
                    <div class="gap-2 w-full mb-4">
                        <input wire:model="email" type="email" placeholder="Correo electrónico" class="w-full p-2 rounded-md border border-blue-300 mb-2">
                        <input wire:model="password" type="password" placeholder="Contraseña" class="w-full p-2 rounded-md border border-blue-300 mb-2">
                    </div>

                    <button type="submit" class="w-full bg-blue-500 text-white p-2 rounded-md">Iniciar sesión</button>

                    @if ($errors->any())
                        <div
                            wire:key="login-errors-{{ $attempts }}"
                            x-data="{ show: true }"
                            x-init="setTimeout(() => { show = false }, 5000)"
                            x-show="show"
                            x-transition.opacity.duration.300ms
                            class="mt-2"
                        >
                            @error('email')
                                <span class="text-red-500">{{ $message }}</span>
                            @enderror

                            @error('password')
                                <span class="text-red-500">{{ $message }}</span>
                            @enderror
                        </div>
                    @endif
                </form>
            </div>
        </div>
    </div>
</div>
