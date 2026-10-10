<?php

namespace App\Livewire\Blockchain;

use App\Models\BlockchainTransaction;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

class BlockchainExplorer extends Component
{
    use WithPagination;

    #[Url]
    public string $cari = '';

    public function updatedCari()
    {
        $this->resetPage();
    }

    public function render()
    {
        return view('livewire.blockchain.blockchain-explorer', ['transactions' => BlockchainTransaction::with('campaign')->when($this->cari, fn ($q) => $q->where(fn ($x) => $x->where('transaction_hash', 'like', '%'.$this->cari.'%')->orWhere('activity_type', 'like', '%'.$this->cari.'%')))->latest('timestamp')->paginate(15)])->layout('layouts.app', ['title' => 'Explorer Blockchain']);
    }
}
