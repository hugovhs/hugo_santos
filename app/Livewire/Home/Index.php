<?php

namespace App\Livewire\Home;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;

class Index extends Component
{
    #[Layout('components.layouts.home')]
    #[Title('Inicio')]
    public function render()
    {
        return view('livewire.home.index');
    }
}
