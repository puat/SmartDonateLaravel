<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Campaign extends Model
{
    protected $fillable = ['user_id', 'category_id', 'title', 'slug', 'description', 'location', 'target_amount', 'collected_amount', 'released_amount', 'escrow_amount', 'start_date', 'end_date', 'image', 'status', 'verification_status'];

    protected function casts(): array
    {
        return ['start_date' => 'date', 'end_date' => 'date'];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function donations()
    {
        return $this->hasMany(Donation::class);
    }

    public function milestones()
    {
        return $this->hasMany(Milestone::class)->orderBy('sequence');
    }

    public function blockchainTransactions()
    {
        return $this->hasMany(BlockchainTransaction::class);
    }

    public function fundReleases()
    {
        return $this->hasMany(FundRelease::class);
    }

    public function getProgressAttribute(): float
    {
        return $this->target_amount ? min(100, round($this->collected_amount / $this->target_amount * 100, 1)) : 0;
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }
}
