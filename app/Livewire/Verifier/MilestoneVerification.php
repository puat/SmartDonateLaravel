<?php

namespace App\Livewire\Verifier;

use App\Models\Milestone;
use App\Models\MilestoneVerification as Verification;
use App\Services\AuditService;
use App\Services\BlockchainService;
use App\Services\FundReleaseService;
use Livewire\Component;
use Livewire\WithPagination;

class MilestoneVerification extends Component
{
    use WithPagination;

    public array $notes = [];

    public function decide(int $id, string $decision)
    {
        $m = Milestone::with('campaign')->findOrFail($id);
        abort_unless(in_array($decision, ['DISETUJUI', 'DITOLAK']), 422);
        $note = $this->notes[$id] ?? '';
        $this->validateOnly("notes.$id", ["notes.$id" => 'required|min:5'], ["notes.$id.required" => 'Catatan verifier wajib diisi.']);
        Verification::create(['milestone_id' => $id, 'verifier_id' => auth()->id(), 'status' => $decision, 'notes' => $note, 'verified_at' => now()]);
        $m->update(['status' => $decision === 'DISETUJUI' ? 'TERVERIFIKASI' : 'DITOLAK', 'verified_at' => now()]);
        if ($decision === 'DISETUJUI') {
            app(BlockchainService::class)->recordMilestoneVerification($m);
        }app(AuditService::class)->record('DIVERIFIKASI', 'MILESTONE', $id, "Milestone {$decision}: {$note}");
        session()->flash('success', 'Keputusan milestone disimpan.');
    }

    public function release(int $id, FundReleaseService $service)
    {
        $service->release(Milestone::findOrFail($id), auth()->user());
        session()->flash('success', 'Dana milestone berhasil dicairkan dan tercatat pada audit blockchain.');
    }

    public function render()
    {
        return view('livewire.verifier.milestone-verification', ['milestones' => Milestone::with(['campaign', 'evidences'])->whereIn('status', ['DALAM_VERIFIKASI', 'TERVERIFIKASI'])->latest()->paginate(10)])->layout('layouts.dashboard',['title' => 'Verifikasi Milestone']);
    }
}
