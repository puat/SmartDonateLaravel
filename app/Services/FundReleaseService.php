<?php

namespace App\Services;

use App\Models\Campaign;
use App\Models\FundRelease;
use App\Models\Milestone;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class FundReleaseService
{
    public function release(Milestone $milestone, User $authorizer): FundRelease
    {
        return DB::transaction(function () use ($milestone, $authorizer) {
            $milestone = Milestone::lockForUpdate()->findOrFail($milestone->id);
            $campaign = Campaign::lockForUpdate()->findOrFail($milestone->campaign_id);
            if ($milestone->status !== 'TERVERIFIKASI') {
                throw ValidationException::withMessages(['milestone' => 'Hanya milestone terverifikasi yang dapat dicairkan.']);
            }
            if (FundRelease::where('milestone_id', $milestone->id)->exists()) {
                throw ValidationException::withMessages(['milestone' => 'Dana milestone ini sudah pernah dicairkan atau diproses.']);
            }
            if ($milestone->amount > $campaign->escrow_amount || $campaign->released_amount + $milestone->amount > $campaign->collected_amount) {
                throw ValidationException::withMessages(['milestone' => 'Saldo escrow atau dana terkumpul tidak mencukupi.']);
            }
            app(BlockchainService::class)->recordFundRelease($milestone, 'FUND_RELEASE_AUTHORIZED');
            $chain = app(BlockchainService::class)->recordFundRelease($milestone, 'FUND_RELEASED');
            $release = FundRelease::create(['campaign_id' => $campaign->id, 'milestone_id' => $milestone->id, 'amount' => $milestone->amount, 'status' => 'DICAIRKAN', 'authorized_by' => $authorizer->id, 'transaction_hash' => $chain->transaction_hash, 'block_number' => $chain->block_number, 'released_at' => now()]);
            $campaign->decrement('escrow_amount', $milestone->amount);
            $campaign->increment('released_amount', $milestone->amount);
            $milestone->update(['status' => 'DICAIRKAN', 'released_at' => now()]);
            app(AuditService::class)->record('DICAIRKAN', 'MILESTONE', $milestone->id, "Dana milestone {$milestone->title} dicairkan.");

            $nextMilestone = $campaign->milestones()
                ->where('sequence', '>', $milestone->sequence)
                ->orderBy('sequence')
                ->lockForUpdate()
                ->first();

            if ($nextMilestone?->status === 'TERKUNCI') {
                $nextMilestone->update(['status' => 'TERBUKA']);
                app(AuditService::class)->record(
                    'DIBUKA',
                    'MILESTONE',
                    $nextMilestone->id,
                    "Milestone {$nextMilestone->title} dibuka setelah tahap sebelumnya dicairkan."
                );
            }

            return $release;
        });
    }
}
