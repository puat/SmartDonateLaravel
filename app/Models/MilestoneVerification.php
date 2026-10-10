<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MilestoneVerification extends Model
{
    protected $fillable = ['milestone_id', 'verifier_id', 'status', 'notes', 'verified_at'];

    protected function casts(): array
    {
        return ['verified_at' => 'datetime'];
    }

    public function milestone()
    {
        return $this->belongsTo(Milestone::class);
    }

    public function verifier()
    {
        return $this->belongsTo(User::class, 'verifier_id');
    }
}
