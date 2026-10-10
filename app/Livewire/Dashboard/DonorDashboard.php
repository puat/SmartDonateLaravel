<?php

namespace App\Livewire\Dashboard;

use App\Models\Donation;
use Livewire\Component;

class DonorDashboard extends Component
{
    public function render()
    {
        $q = Donation::where('user_id', auth()->id());

        return view('livewire.dashboard.donor-dashboard', ['stats' => ['total' => (clone $q)->where('payment_status', 'BERHASIL')->sum('amount'), 'program' => (clone $q)->distinct('campaign_id')->count('campaign_id'), 'aktif' => (clone $q)->whereHas('campaign', fn ($x) => $x->where('status', 'AKTIF'))->distinct('campaign_id')->count('campaign_id'), 'blockchain' => (clone $q)->where('blockchain_status', 'TERKONFIRMASI')->count()], 'donations' => $q->with('campaign')->latest('donated_at')->limit(8)->get()])->layout('layouts.dashboard', ['title' => 'Dasbor Donatur']);
    }
}
