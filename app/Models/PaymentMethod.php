<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PaymentMethod extends Model
{
    protected $fillable = ['name', 'type', 'bank_name', 'account_number', 'account_name', 'status'];

    public function donations()
    {
        return $this->hasMany(Donation::class);
    }
}
