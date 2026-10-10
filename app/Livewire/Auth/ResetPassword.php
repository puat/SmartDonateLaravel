<?php

namespace App\Livewire\Auth;

use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Livewire\Component;

class ResetPassword extends Component
{
    public string $token;

    public string $email = '';

    public string $password = '';

    public string $password_confirmation = '';

    public function mount(string $token)
    {
        $this->token = $token;
        $this->email = request('email', '');
    }

    public function resetPassword()
    {
        $this->validate(['email' => 'required|email', 'password' => 'required|min:8|confirmed']);
        $result = Password::reset(['email' => $this->email, 'password' => $this->password, 'password_confirmation' => $this->password_confirmation, 'token' => $this->token], function ($user, $password) {
            $user->forceFill(['password' => $password, 'remember_token' => Str::random(60)])->save();
            event(new PasswordReset($user));
        });
        if ($result !== Password::PASSWORD_RESET) {
            $this->addError('email', 'Tautan tidak valid atau kedaluwarsa.');

            return;
        } session()->flash('success', 'Kata sandi berhasil diperbarui.');

        return $this->redirectRoute('login');
    }

    public function render()
    {
        return view('livewire.auth.reset-password')->layout('layouts.app', ['title' => 'Atur Ulang Kata Sandi']);
    }
}
