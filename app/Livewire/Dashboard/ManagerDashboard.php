<?php

namespace App\Livewire\Dashboard;

use App\Models\Campaign;
use Livewire\Component;

class ManagerDashboard extends Component
{
    public function render()
    {
        $q = Campaign::where('user_id', auth()->id());

        return view('livewire.dashboard.manager-dashboard', ['stats' => ['program' => (clone $q)->count(), 'terkumpul' => (clone $q)->sum('collected_amount'), 'escrow' => (clone $q)->sum('escrow_amount'), 'dicairkan' => (clone $q)->sum('released_amount'), 'milestone' => (clone $q)->withCount('milestones')->get()->sum('milestones_count'), 'menunggu' => (clone $q)->where('verification_status', 'MENUNGGU')->count()], 'campaigns' => $q->withCount('milestones')->latest()->get()])->layout('layouts.dashboard', ['title' => 'Dasbor Pengelola']);
    }
}
