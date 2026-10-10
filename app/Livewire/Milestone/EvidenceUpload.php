<?php

namespace App\Livewire\Milestone;

use App\Models\Milestone;
use App\Services\AuditService;
use App\Services\BlockchainService;
use Illuminate\View\View;
use Livewire\Component;
use Livewire\WithFileUploads;

class EvidenceUpload extends Component
{
    use WithFileUploads;

    public Milestone $milestone;

    public string $title = '';

    public string $description = '';

    public $file;

    public function mount(Milestone $milestone): void
    {
        abort_unless($milestone->campaign->user_id === auth()->id() || auth()->user()->isRole('ADMIN'), 403);
        $this->milestone = $milestone;
    }

    public function save(): void
    {
        $this->validate(['title' => 'required|max:150', 'description' => 'nullable|max:1000', 'file' => 'required|file|mimes:pdf,jpg,jpeg,png|max:5120'], ['file.required' => 'Pilih berkas bukti.', 'file.mimes' => 'Bukti harus berupa PDF, JPG, JPEG, atau PNG.', 'file.max' => 'Ukuran bukti maksimal 5 MB.']);
        $hash = hash_file('sha256', $this->file->getRealPath());
        $path = $this->file->store('evidences', 'public');
        $evidence = $this->milestone->evidences()->create(['user_id' => auth()->id(), 'title' => $this->title, 'description' => $this->description, 'file_path' => $path, 'file_name' => $this->file->getClientOriginalName(), 'file_hash' => $hash, 'uploaded_at' => now()]);
        app(BlockchainService::class)->recordEvidence($evidence);
        app(AuditService::class)->record('DIUNGGAH', 'BUKTI', $evidence->id, "Bukti {$evidence->file_name} diunggah dengan hash SHA-256.");
        $this->reset('title', 'description', 'file');
        session()->flash('success', 'Bukti tersimpan dan hash SHA-256 berhasil dibuat.');
    }

    public function render(): View
    {
        return view('livewire.milestone.evidence-upload', ['evidences' => $this->milestone->evidences()->latest()->get()])->layout('layouts.dashboard', ['title' => 'Unggah Bukti']);
    }
}
