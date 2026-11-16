<?php

namespace App\Livewire;

use Livewire\Component;

class Dashboard extends Component
{
    public function render()
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        return view('livewire.dashboard')->layout('layouts.admin');
    }
}
