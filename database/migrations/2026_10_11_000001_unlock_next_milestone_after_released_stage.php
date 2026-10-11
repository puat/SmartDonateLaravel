<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('milestones')
            ->where('status', 'TERKUNCI')
            ->orderBy('campaign_id')
            ->orderBy('sequence')
            ->get()
            ->each(function (object $milestone): void {
                $previousMilestone = DB::table('milestones')
                    ->where('campaign_id', $milestone->campaign_id)
                    ->where('sequence', '<', $milestone->sequence)
                    ->orderByDesc('sequence')
                    ->first();

                if ($previousMilestone?->status === 'DICAIRKAN') {
                    DB::table('milestones')
                        ->where('id', $milestone->id)
                        ->update(['status' => 'TERBUKA', 'updated_at' => now()]);
                }
            });
    }

    public function down(): void
    {
        // Status yang sudah terbuka tidak dikunci kembali agar progres pengguna tidak hilang.
    }
};
