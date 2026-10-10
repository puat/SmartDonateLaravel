<?php

namespace App\Livewire\Campaign;

use App\Models\Campaign;
use App\Models\Category;
use App\Services\AuditService;
use App\Services\BlockchainService;
use Illuminate\Support\Str;
use Livewire\Component;
use Livewire\WithFileUploads;

class CampaignCreate extends Component
{
    use WithFileUploads;

    public string $title = '';

    public string $location = '';

    public string $description = '';

    public string $target_amount = '';

    public string $start_date = '';

    public string $end_date = '';

    public $category_id;

    public $image;

    public function save()
    {
        $data = $this->validate(['title' => 'required|min:8|max:180', 'category_id' => 'required|exists:categories,id', 'location' => 'required|max:150', 'description' => 'required|min:50', 'target_amount' => 'required|integer|min:1000000', 'start_date' => 'required|date', 'end_date' => 'required|date|after:start_date', 'image' => 'nullable|image|max:3072'], ['title.required' => 'Judul program wajib diisi.', 'description.min' => 'Deskripsi minimal 50 karakter.', 'target_amount.min' => 'Target minimal Rp1.000.000.', 'end_date.after' => 'Tanggal berakhir harus setelah tanggal mulai.', 'image.max' => 'Ukuran gambar maksimal 3 MB.']);
        if ($this->image) {
            $data['image'] = $this->image->store('campaigns', 'public');
        } $campaign = Campaign::create($data + ['user_id' => auth()->id(), 'slug' => Str::slug($this->title).'-'.Str::lower(Str::random(5)), 'status' => 'DRAFT', 'verification_status' => 'MENUNGGU']);
        app(BlockchainService::class)->recordCampaign($campaign);
        app(AuditService::class)->record('DIBUAT', 'PROGRAM', $campaign->id, "Program {$campaign->title} dibuat.");
        session()->flash('success', 'Program tersimpan sebagai draf dan menunggu verifikasi.');

        return $this->redirectRoute('dashboard.pengelola');
    }

    public function render()
    {
        return view('livewire.campaign.campaign-create', ['categories' => Category::orderBy('name')->get()])->layout('layouts.dashboard', ['title' => 'Buat Program']);
    }
}
