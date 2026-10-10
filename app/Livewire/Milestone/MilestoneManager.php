<?php

namespace App\Livewire\Milestone;

use App\Models\Campaign;
use App\Services\AuditService;
use App\Services\BlockchainService;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Livewire\Component;

class MilestoneManager extends Component
{
    public Campaign $campaign;

    public string $title = '';

    public string $description = '';

    public string $percentage = '';

    public string $amount = '';

    public string $sequence = '';

    public function mount(Campaign $campaign): void
    {
        abort_unless($campaign->user_id === auth()->id() || auth()->user()->isRole('ADMIN'), 403);
        $this->campaign = $campaign;
        $this->setNextSequence();
    }

    public function save(): void
    {
        $data = $this->validate([
            'title' => ['required', 'max:150'],
            'description' => ['required', 'min:10'],
            'percentage' => ['required', 'integer', 'min:1', 'max:100'],
            'amount' => ['required', 'integer', 'min:1'],
            'sequence' => [
                'required',
                'integer',
                'min:1',
                Rule::unique('milestones', 'sequence')->where('campaign_id', $this->campaign->id),
            ],
        ], [
            'title.required' => 'Nama tahap wajib diisi.',
            'description.required' => 'Deskripsi wajib diisi.',
            'description.min' => 'Deskripsi minimal 10 karakter.',
            'percentage.required' => 'Persentase wajib diisi.',
            'percentage.integer' => 'Persentase harus berupa angka bulat.',
            'percentage.min' => 'Persentase minimal 1%.',
            'percentage.max' => 'Persentase maksimal 100%.',
            'amount.required' => 'Nominal Rupiah wajib diisi.',
            'amount.integer' => 'Nominal Rupiah harus berupa angka bulat tanpa titik atau koma.',
            'amount.min' => 'Nominal Rupiah minimal Rp1.',
            'sequence.required' => 'Urutan wajib diisi.',
            'sequence.integer' => 'Urutan harus berupa angka bulat.',
            'sequence.min' => 'Urutan minimal 1.',
            'sequence.unique' => 'Urutan tersebut sudah digunakan pada program ini.',
        ]);
        $total = $this->campaign->milestones()->sum('percentage') + (int) $data['percentage'];
        if ($total > 100) {
            $this->addError('percentage', 'Total persentase milestone tidak boleh melebihi 100%.');

            return;
        }

        $milestone = $this->campaign->milestones()->create($data + ['status' => $this->campaign->milestones()->exists() ? 'TERKUNCI' : 'TERBUKA']);
        app(BlockchainService::class)->recordMilestone($milestone);
        app(AuditService::class)->record('DIBUAT', 'MILESTONE', $milestone->id, "Milestone {$milestone->title} dibuat.");
        $this->reset('title', 'description', 'percentage', 'amount', 'sequence');
        $this->campaign->refresh();
        $this->setNextSequence();
        session()->flash('success', $total === 100 ? 'Milestone lengkap 100%.' : 'Milestone ditambahkan. Total saat ini '.$total.'%.');
    }

    public function submit(int $id): void
    {
        $m = $this->campaign->milestones()->findOrFail($id);
        abort_unless(in_array($m->status, ['TERBUKA', 'DITOLAK']), 422);
        if ((int) $this->campaign->milestones()->sum('percentage') !== 100) {
            $this->addError('percentage', 'Total seluruh milestone harus tepat 100% sebelum diajukan.');

            return;
        }

        abort_if($m->evidences()->doesntExist(), 422, 'Unggah bukti sebelum mengajukan milestone.');
        $m->update(['status' => 'DALAM_VERIFIKASI', 'submitted_at' => now()]);
        session()->flash('success', 'Milestone diajukan kepada verifier.');
    }

    public function render(): View
    {
        return view('livewire.milestone.milestone-manager', ['milestones' => $this->campaign->milestones()->with('evidences')->get()])->layout('layouts.dashboard', ['title' => 'Kelola Milestone']);
    }

    private function setNextSequence(): void
    {
        $this->sequence = (string) (((int) $this->campaign->milestones()->max('sequence')) + 1);
    }
}
