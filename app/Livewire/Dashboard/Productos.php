<?php

namespace App\Livewire\Dashboard;

use Livewire\Component;
use Livewire\Attributes\Title;
use Livewire\Attributes\Layout;

class Productos extends Component
{
    #[Title('Productos')]
    #[Layout('components.layouts.admin')]
    public function render()
    {
        return view('livewire.dashboard.productos');
    }
}
