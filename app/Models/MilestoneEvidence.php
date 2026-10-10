<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MilestoneEvidence extends Model
{
    protected $table = 'milestone_evidences';

    protected $fillable = ['milestone_id', 'user_id', 'title', 'description', 'file_path', 'file_name', 'file_hash', 'uploaded_at'];

    protected function casts(): array
    {
        return ['uploaded_at' => 'datetime'];
    }

    public function milestone()
    {
        return $this->belongsTo(Milestone::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
