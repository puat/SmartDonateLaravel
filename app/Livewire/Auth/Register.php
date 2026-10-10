<?php

namespace App\Livewire\Auth;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Register extends Component
{
    public string $name = '';

    public string $email = '';

    public string $phone = '';

    public string $password = '';

    public string $password_confirmation = '';

    public function register()
    {
        $data = $this->validate(['name' => 'required|min:3', 'email' => 'required|email|unique:users', 'phone' => 'nullable|max:30', 'password' => 'required|min:8|confirmed'], ['name.required' => 'Nama wajib diisi.', 'email.required' => 'Email wajib diisi.', 'email.email' => 'Format email tidak valid.', 'email.unique' => 'Email sudah terdaftar.', 'password.required' => 'Kata sandi wajib diisi.', 'password.min' => 'Kata sandi minimal 8 karakter.', 'password.confirmed' => 'Konfirmasi kata sandi tidak sama.']);
        $user = User::create($data + ['role' => 'DONATUR', 'status' => 'AKTIF']);
        Auth::login($user);

        return $this->redirectRoute('dashboard.donatur', navigate: true);
    }

    public function render()
    {
        return view('livewire.auth.register')->layout('layouts.app', ['title' => 'Daftar']);
    }
}
