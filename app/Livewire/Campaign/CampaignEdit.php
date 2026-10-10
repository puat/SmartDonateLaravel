<?php

namespace App\Livewire\Campaign;

use App\Models\Campaign;
use App\Models\Category;
use App\Services\AuditService;
use Livewire\Component;
use Livewire\WithFileUploads;

class CampaignEdit extends Component
{
    use WithFileUploads;

    public Campaign $campaign;

    public string $title;

    public string $location;

    public string $description;

    public string $target_amount;

    public string $start_date;

    public string $end_date;

    public $category_id;

    public $image;

    public function mount(Campaign $campaign)
    {
        abort_unless($campaign->user_id === auth()->id() || auth()->user()->isRole('ADMIN'), 403);
        $this->campaign = $campaign;
        $this->fill(['title' => $campaign->title, 'category_id' => $campaign->category_id, 'location' => $campaign->location, 'description' => $campaign->description, 'target_amount' => (string) $campaign->target_amount, 'start_date' => $campaign->start_date->format('Y-m-d'), 'end_date' => $campaign->end_date->format('Y-m-d')]);
    }

    public function save()
    {
        $data = $this->validate(['title' => 'required|min:8|max:180', 'category_id' => 'required|exists:categories,id', 'location' => 'required|max:150', 'description' => 'required|min:50', 'target_amount' => 'required|integer|min:1000000', 'start_date' => 'required|date', 'end_date' => 'required|date|after:start_date', 'image' => 'nullable|image|max:3072']);
        if ($this->image) {
            $data['image'] = $this->image->store('campaigns', 'public');
        } else {
            unset($data['image']);
        }$this->campaign->update($data);
        app(AuditService::class)->record('DIPERBARUI', 'PROGRAM', $this->campaign->id, "Program {$this->campaign->title} diperbarui.");
        session()->flash('success', 'Perubahan program disimpan.');

        return $this->redirectRoute('dashboard.pengelola');
    }

    public function render()
    {
        return view('livewire.campaign.campaign-edit', ['categories' => Category::orderBy('name')->get()])->layout('layouts.dashboard', ['title' => 'Edit Program']);
    }
}
