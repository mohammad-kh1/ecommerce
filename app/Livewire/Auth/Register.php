<?php

namespace App\Livewire\Auth;

use App\Models\User;
use Livewire\Attributes\Title;
use Livewire\Attributes\Validate;
use Livewire\Component;

#[Title("register")]
class Register extends Component
{

    #[Validate("required|max:100|min:3")]
    public string $name;
    #[Validate("required|unique:users")]
    public string $email;
    #[Validate("required|min:6|max:8")]
    public string $password;

    public function save()
    {
        $this->validate();
        $user = User::create([
            "name" =>$this->name,
            "email" =>$this->email,
            "password" =>bcrypt($this->password)
        ]);
        //login
        auth()->login($user);
        return redirect()->intended();
    }
    public function render()
    {
        return view('livewire.auth.register');
    }
}
