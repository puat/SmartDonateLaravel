<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BlockchainTransaction extends Model
{
    protected $fillable = ['transaction_hash', 'block_number', 'campaign_id', 'donation_id', 'milestone_id', 'activity_type', 'amount', 'evidence_hash', 'status', 'timestamp', 'metadata'];

    protected function casts(): array
    {
        return ['timestamp' => 'datetime', 'metadata' => 'array'];
    }

    public function campaign()
    {
        return $this->belongsTo(Campaign::class);
    }

    public function donation()
    {
        return $this->belongsTo(Donation::class);
    }

    public function milestone()
    {
        return $this->belongsTo(Milestone::class);
    }
}
