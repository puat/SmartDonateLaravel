<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Milestone extends Model
{
    protected $fillable = ['campaign_id', 'title', 'description', 'percentage', 'amount', 'sequence', 'status', 'submitted_at', 'verified_at', 'released_at'];

    protected function casts(): array
    {
        return ['submitted_at' => 'datetime', 'verified_at' => 'datetime', 'released_at' => 'datetime'];
    }

    public function campaign()
    {
        return $this->belongsTo(Campaign::class);
    }

    public function evidences()
    {
        return $this->hasMany(MilestoneEvidence::class);
    }

    public function verifications()
    {
        return $this->hasMany(MilestoneVerification::class);
    }

    public function fundRelease()
    {
        return $this->hasOne(FundRelease::class);
    }
}
