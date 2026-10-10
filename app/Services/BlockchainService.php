<?php

namespace App\Services;

use App\Models\BlockchainTransaction;
use App\Models\Campaign;
use App\Models\Donation;
use App\Models\Milestone;
use App\Models\MilestoneEvidence;

class BlockchainService
{
    private function record(string $type, Campaign $campaign, int $amount = 0, ?Donation $donation = null, ?Milestone $milestone = null, ?string $evidenceHash = null): BlockchainTransaction
    {
        $simulation = config('blockchain.mode') !== 'ethereum';

        return BlockchainTransaction::create(['transaction_hash' => '0x'.bin2hex(random_bytes(32)), 'block_number' => random_int(18000000, 19999999), 'campaign_id' => $campaign->id, 'donation_id' => $donation?->id, 'milestone_id' => $milestone?->id, 'activity_type' => $type, 'amount' => $amount, 'evidence_hash' => $evidenceHash, 'status' => 'TERKONFIRMASI', 'timestamp' => now(), 'metadata' => ['mode' => $simulation ? 'SIMULATION' : 'ETHEREUM', 'catatan' => $simulation ? 'Catatan demo—bukan transaksi blockchain riil.' : null]]);
    }

    public function recordDonation(Donation $donation)
    {
        return $this->record('DONATION', $donation->campaign, $donation->amount, $donation);
    }

    public function recordCampaign(Campaign $campaign)
    {
        return $this->record('CAMPAIGN_CREATED', $campaign);
    }

    public function recordMilestone(Milestone $milestone)
    {
        return $this->record('MILESTONE_CREATED', $milestone->campaign, $milestone->amount, null, $milestone);
    }

    public function recordEvidence(MilestoneEvidence $evidence)
    {
        return $this->record('EVIDENCE_SUBMITTED', $evidence->milestone->campaign, 0, null, $evidence->milestone, $evidence->file_hash);
    }

    public function recordMilestoneVerification(Milestone $milestone)
    {
        return $this->record('MILESTONE_VERIFIED', $milestone->campaign, $milestone->amount, null, $milestone);
    }

    public function recordFundRelease(Milestone $milestone, string $type = 'FUND_RELEASED')
    {
        return $this->record($type, $milestone->campaign, $milestone->amount, null, $milestone);
    }
}
