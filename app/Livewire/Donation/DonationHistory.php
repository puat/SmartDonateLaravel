<?php

namespace App\Livewire\Donation;

use App\Models\Donation;
use Livewire\Component;
use Livewire\WithPagination;

class DonationHistory extends Component
{
    use WithPagination;

    public function render()
    {
        return view('livewire.donation.donation-history', ['donations' => Donation::with(['campaign', 'paymentMethod'])->where('user_id', auth()->id())->latest('donated_at')->paginate(10)])->layout('layouts.dashboard', ['title' => 'Riwayat Donasi']);
    }
}
