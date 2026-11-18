<?php

namespace App\Livewire\Home;

use App\Services\CarritoService;
use Livewire\Attributes\On;
use Livewire\Component;

class ContadorCarrito extends Component
{
    public int $total = 0;

    public function mount(): void
    {
        $this->total = CarritoService::totalUnidades();
    }

    #[On('carrito-actualizado')]
    public function actualizar(?int $total = null): void
    {
        $this->total = $total ?? CarritoService::totalUnidades();
    }

    public function render()
    {
        return view('livewire.home.contador-carrito');
    }
}
