<?php

namespace App\Livewire;

use Livewire\Component;

class Login extends Component
{
    public $email;
    public $password;
    public $attempts = 0;

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
