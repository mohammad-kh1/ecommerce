<?php

namespace App\Livewire\Auth;

use Livewire\Attributes\Validate;
use Livewire\Component;

class Login extends Component
{
    #[Validate("required|email")]
    public string $email;
    #[Validate("required")]
    public string $password;

    public function save()
    {
        $this->validate();

        if(!auth()->attempt(["email" => $this->email , "password" => $this->password])){
            session()->flash("error","invalid credentials");
            return;
        }

        return redirect()->intended();
    }
    public function render()
    {
        return view('livewire.auth.login');
    }
}
