<?php

namespace App\Livewire\Dashboard;

use App\Models\Producto;

use Livewire\Component;
use Livewire\Attributes\Title;
use Livewire\Attributes\Layout;
use Livewire\WithPagination;

class Productos extends Component
{
    use WithPagination;

    #[Title('Productos')]
    #[Layout('components.layouts.admin')]
    public function render()
    {
        return view('livewire.dashboard.productos', [
            'productos' => Producto::orderBy('id', 'desc')->paginate(10)
        ]);
    }
}
