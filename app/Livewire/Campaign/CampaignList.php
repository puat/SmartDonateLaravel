<?php

namespace App\Livewire\Campaign;

use App\Models\Campaign;
use App\Models\Category;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

class CampaignList extends Component
{
    use WithPagination;

    #[Url]
    public string $cari = '';

    #[Url]
    public string $kategori = '';

    #[Url]
    public string $lokasi = '';

    #[Url]
    public string $status = 'AKTIF';

    #[Url]
    public string $urut = 'terbaru';

    public function updated($property)
    {
        if (in_array($property, ['cari', 'kategori', 'lokasi', 'status', 'urut'])) {
            $this->resetPage();
        }
    }

    public function render()
    {
        $q = Campaign::with(['category'])->withCount('donations')->when($this->cari, fn ($q) => $q->where(fn ($x) => $x->where('title', 'like', '%'.$this->cari.'%')->orWhere('description', 'like', '%'.$this->cari.'%')))->when($this->kategori, fn ($q) => $q->where('category_id', $this->kategori))->when($this->lokasi, fn ($q) => $q->where('location', 'like', '%'.$this->lokasi.'%'))->when($this->status, fn ($q) => $q->where('status', $this->status));
        $q = match ($this->urut) {
            'dana' => $q->orderByDesc('collected_amount'),'target' => $q->orderByDesc('target_amount'),'berakhir' => $q->orderBy('end_date'),default => $q->latest()
        };

        return view('livewire.campaign.campaign-list', ['campaigns' => $q->paginate(9), 'categories' => Category::orderBy('name')->get()])->layout('layouts.app', ['title' => 'Jelajahi Program']);
    }
}
