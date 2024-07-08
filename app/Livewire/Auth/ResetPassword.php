<?php

namespace App\Livewire\Auth;

use App\Models\User;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Support\Facades\Password;
use Livewire\Attributes\Url;
use Livewire\Attributes\Validate;
use Livewire\Component;

class ResetPassword extends Component
{

    #[Validate("required|confirmed")]
    public string $password;

    #[Validate("required")]
    public string $password_confirmation;
    #[Url]
    #[Validate("email|required")]
    public string $email;
    public string $token;

    public function mount($token)
    {
        $this->$token =$token;
    }

    public function save()
    {
        $status = Password::reset([
            "email" => $this->email,
            "password" => $this->password,
            "password_confirmation"=>$this->password_confirmation,
            "token" => $this->token
        ] , function(User $user , string $password){
                $password = $this->password;
                $user->forceFill([
                    "password" => bcrypt($this->password)
                ])->setRememberToken(str()->random(60));
                $user->save();
                event(new PasswordReset($user));
        });
        return $status === Password::PASSWORD_RESET ? redirect("/login") : session()->flash("error" , "Error");
    }
    public function render()
    {
        return view('livewire.auth.reset-password');
    }
}
