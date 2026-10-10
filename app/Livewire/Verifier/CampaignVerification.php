<?php

namespace App\Livewire\Verifier;

use App\Models\Campaign;
use App\Services\AuditService;
use Livewire\Component;
use Livewire\WithPagination;

class CampaignVerification extends Component
{
    use WithPagination;

    public string $notes = '';

    public function decide(int $id, string $decision)
    {
        $campaign = Campaign::findOrFail($id);
        abort_unless(in_array($decision, ['TERVERIFIKASI', 'DITOLAK']), 422);
        $campaign->update(['verification_status' => $decision, 'status' => $decision === 'TERVERIFIKASI' ? 'AKTIF' : 'DITOLAK']);
        app(AuditService::class)->record('DIVERIFIKASI', 'PROGRAM', $id, "Program {$campaign->title}: {$decision}. Catatan: {$this->notes}");
        $this->reset('notes');
        session()->flash('success', 'Keputusan verifikasi program disimpan.');
    }

    public function render()
    {
        return view('livewire.verifier.campaign-verification', ['campaigns' => Campaign::with(['category', 'user'])->where('verification_status', 'MENUNGGU')->latest()->paginate(10)])->layout('layouts.dashboard', ['title' => 'Verifikasi Program']);
    }
}
