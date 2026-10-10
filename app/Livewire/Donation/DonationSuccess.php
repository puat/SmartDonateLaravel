<?php

namespace App\Livewire\Donation;

use App\Models\Donation;
use Livewire\Component;

class DonationSuccess extends Component
{
    public Donation $donation;

    public function mount(Donation $donation)
    {
        abort_unless($donation->user_id === auth()->id() || auth()->user()->isRole('ADMIN'), 403);
        $this->donation = $donation->load(['campaign', 'paymentMethod']);
    }

    public function render()
    {
        return view('livewire.donation.donation-success')->layout('layouts.app', ['title' => 'Donasi Berhasil']);
    }
}
