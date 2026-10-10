<?php

namespace App\Services;

use App\Models\Campaign;
use App\Models\Donation;
use App\Models\PaymentMethod;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class DonationService
{
    public function create(User $user, Campaign $campaign, int $amount, PaymentMethod $method): Donation
    {
        return DB::transaction(function () use ($user, $campaign, $amount, $method) {
            $campaign = Campaign::lockForUpdate()->findOrFail($campaign->id);
            $donation = Donation::create(['donation_code' => 'TMP-'.Str::uuid(), 'campaign_id' => $campaign->id, 'user_id' => $user->id, 'amount' => $amount, 'payment_method_id' => $method->id, 'payment_status' => 'BERHASIL', 'blockchain_status' => 'MENUNGGU', 'donated_at' => now()]);
            $donation->update(['donation_code' => 'DON-'.now()->year.'-'.str_pad((string) $donation->id, 6, '0', STR_PAD_LEFT)]);
            $campaign->increment('collected_amount', $amount);
            $campaign->increment('escrow_amount', $amount);
            $chain = app(BlockchainService::class)->recordDonation($donation->load('campaign'));
            $donation->update(['blockchain_status' => 'TERKONFIRMASI', 'transaction_hash' => $chain->transaction_hash, 'block_number' => $chain->block_number]);
            app(AuditService::class)->record('DIBUAT', 'DONASI', $donation->id, "Donasi simulasi {$donation->donation_code} berhasil dibuat.");

            return $donation;
        });
    }
}
