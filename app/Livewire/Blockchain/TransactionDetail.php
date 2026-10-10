<?php

namespace App\Livewire\Blockchain;

use App\Models\BlockchainTransaction;
use Livewire\Component;

class TransactionDetail extends Component
{
    public BlockchainTransaction $transaction;

    public function mount(string $transactionHash)
    {
        $this->transaction = BlockchainTransaction::with(['campaign', 'donation', 'milestone'])->where('transaction_hash', $transactionHash)->firstOrFail();
    }

    public function render()
    {
        return view('livewire.blockchain.transaction-detail')->layout('layouts.app', ['title' => 'Detail Transaksi']);
    }
}
