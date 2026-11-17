<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\Attributes\Title;

class Login extends Component
{
    public $email;
    public $password;
    public $attempts = 0;

    #[Title('Iniciar sesión')]
    public function login()
    {
        $this->attempts++;

        $this->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (auth()->attempt(['email' => $this->email, 'password' => $this->password])) {
            return redirect()->route('dashboard');
        } else {
            $this->addError('email', 'Credenciales incorrectas');
        }
    }

    public function render()
    {
        return view('livewire.login');
    }
}
