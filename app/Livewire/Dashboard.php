<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;

class Dashboard extends Component
{
    #[Title('Dashboard')]
    #[Layout('components.layouts.admin')]
    public function render()
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        return view('livewire.dashboard');
    }
}
