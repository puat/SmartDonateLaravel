<?php

namespace App\Livewire\Campaign;

use App\Models\Campaign;
use Livewire\Component;

class CampaignDetail extends Component
{
    public Campaign $campaign;

    public function mount(Campaign $campaign)
    {
        $this->campaign = $campaign->load(['category', 'user', 'milestones.evidences', 'blockchainTransactions']);
    }

    public function render()
    {
        return view('livewire.campaign.campaign-detail')->layout('layouts.app', ['title' => $this->campaign->title]);
    }
}
