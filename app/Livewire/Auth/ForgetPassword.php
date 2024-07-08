<?php

namespace App\Livewire\Auth;

use Illuminate\Support\Facades\Password;
use Livewire\Attributes\Validate;
use Livewire\Component;

class ForgetPassword extends Component
{

    #[Validate("required|exists:users,email")]
    public string $email;

    public function save()
    {
        $this->validate();

        $status = Password::sendResetLink(["email" => $this->email]);

        if($status === Password::RESET_LINK_SENT){
            session()->flash("success" , "Password link sent");
            $this->email = "";
        }
    }
    public function render()
    {
        return view('livewire.auth.forget-password');
    }
}
