<?php

namespace App\Livewire\Auth;

use Illuminate\Support\Facades\Password;
use Livewire\Component;

class ForgotPassword extends Component
{
    public string $email = '';

    public string $status = '';

    public function send()
    {
        $this->validate(['email' => 'required|email'], ['email.required' => 'Email wajib diisi.']);
        $result = Password::sendResetLink(['email' => $this->email]);
        $this->status = $result === Password::RESET_LINK_SENT ? 'Tautan pengaturan ulang telah dikirim.' : 'Email tidak ditemukan atau permintaan terlalu sering.';
    }

    public function render()
    {
        return view('livewire.auth.forgot-password')->layout('layouts.app', ['title' => 'Lupa Kata Sandi']);
    }
}
