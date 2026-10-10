<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Donation extends Model
{
    protected $fillable = ['donation_code', 'campaign_id', 'user_id', 'amount', 'payment_method_id', 'payment_status', 'blockchain_status', 'transaction_hash', 'block_number', 'donated_at'];

    protected function casts(): array
    {
        return ['donated_at' => 'datetime'];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function campaign()
    {
        return $this->belongsTo(Campaign::class);
    }

    public function paymentMethod()
    {
        return $this->belongsTo(PaymentMethod::class);
    }
}
