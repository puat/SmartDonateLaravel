<?php

namespace App\Livewire\Dashboard;

use App\Models\BlockchainTransaction;
use App\Models\Campaign;
use App\Models\Donation;
use App\Models\FundRelease;
use App\Models\Milestone;
use App\Models\User;
use Livewire\Component;

class AdminDashboard extends Component
{
    public function render()
    {
        return view('livewire.dashboard.admin-dashboard', ['stats' => ['pengguna' => User::count(), 'program' => Campaign::count(), 'donasi' => Donation::where('payment_status', 'BERHASIL')->sum('amount'), 'dicairkan' => FundRelease::where('status', 'DICAIRKAN')->sum('amount'), 'blockchain' => BlockchainTransaction::count(), 'milestone' => Milestone::where('status', 'DALAM_VERIFIKASI')->count()], 'categories' => Campaign::selectRaw('category_id, count(*) total')->with('category')->groupBy('category_id')->get(), 'activities' => BlockchainTransaction::with('campaign')->latest('timestamp')->limit(8)->get()])->layout('layouts.dashboard', ['title' => 'Dasbor Administrator']);
    }
}
