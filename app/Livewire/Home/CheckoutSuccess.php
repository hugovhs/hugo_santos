<?php

namespace App\Livewire\Home;

use App\Models\Pedido;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

class CheckoutSuccess extends Component
{
    public Pedido $pedido;

    public function mount(string $folio): void
    {
        $this->pedido = Pedido::query()->where('folio', $folio)->firstOrFail();
    }

    #[Layout('components.layouts.home')]
    #[Title('Pedido confirmado')]
    public function render()
    {
        return view('livewire.home.checkout-success');
    }
}
