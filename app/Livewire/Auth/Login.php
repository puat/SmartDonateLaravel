<?php

namespace App\Livewire\Auth;

use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Login extends Component
{
    public string $email = '';

    public string $password = '';

    public bool $remember = false;

    public function login()
    {
        $this->validate(['email' => 'required|email', 'password' => 'required'], ['email.required' => 'Email wajib diisi.', 'email.email' => 'Format email tidak valid.', 'password.required' => 'Kata sandi wajib diisi.']);
        if (! Auth::attempt(['email' => $this->email, 'password' => $this->password, 'status' => 'AKTIF'], $this->remember)) {
            $this->addError('email', 'Email atau kata sandi tidak sesuai.');

            return;
        } session()->regenerate();

        return $this->redirectRoute('dashboard', navigate: true);
    }

    public function render()
    {
        return view('livewire.auth.login')->layout('layouts.app', ['title' => 'Masuk']);
    }
}
