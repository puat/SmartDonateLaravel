<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FundRelease extends Model
{
    protected $fillable = ['campaign_id', 'milestone_id', 'amount', 'status', 'authorized_by', 'transaction_hash', 'block_number', 'released_at'];

    protected function casts(): array
    {
        return ['released_at' => 'datetime'];
    }

    public function milestone()
    {
        return $this->belongsTo(Milestone::class);
    }

    public function campaign()
    {
        return $this->belongsTo(Campaign::class);
    }

    public function authorizer()
    {
        return $this->belongsTo(User::class, 'authorized_by');
    }
}
