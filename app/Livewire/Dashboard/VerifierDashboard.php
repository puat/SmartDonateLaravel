<?php

namespace App\Livewire\Dashboard;

use App\Models\Campaign;
use App\Models\Milestone;
use App\Models\MilestoneEvidence;
use App\Models\MilestoneVerification;
use Livewire\Component;

class VerifierDashboard extends Component
{
    public function render()
    {
        return view('livewire.dashboard.verifier-dashboard', ['stats' => ['program' => Campaign::where('verification_status', 'MENUNGGU')->count(), 'milestone' => Milestone::where('status', 'DALAM_VERIFIKASI')->count(), 'bukti' => MilestoneEvidence::whereHas('milestone', fn ($q) => $q->where('status', 'DALAM_VERIFIKASI'))->count(), 'hari_ini' => MilestoneVerification::whereDate('verified_at', today())->count()]])->layout('layouts.dashboard', ['title' => 'Dasbor Verifier']);
    }
}
