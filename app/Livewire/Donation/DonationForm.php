<?php

namespace App\Livewire\Donation;

use App\Models\Campaign;
use App\Models\PaymentMethod;
use App\Services\DonationService;
use Livewire\Component;

class DonationForm extends Component
{
    public Campaign $campaign;

    public int|string $amount = 50000;

    public $payment_method_id;

    public function mount(Campaign $campaign)
    {
        $this->campaign = $campaign;
        $this->payment_method_id = PaymentMethod::where('status', 'AKTIF')->value('id');
    }

    public function choose(int $amount)
    {
        $this->amount = $amount;
    }

    public function donate(DonationService $service)
    {
        $data = $this->validate(['amount' => 'required|integer|min:10000|max:1000000000', 'payment_method_id' => 'required|exists:payment_methods,id'], ['amount.min' => 'Nominal donasi minimal Rp10.000.', 'amount.integer' => 'Nominal harus berupa angka Rupiah.', 'payment_method_id.required' => 'Pilih metode pembayaran.']);
        $donation = $service->create(auth()->user(), $this->campaign, (int) $data['amount'], PaymentMethod::findOrFail($data['payment_method_id']));

        return $this->redirectRoute('donasi.sukses', ['donation' => $donation]);
    }

    public function render()
    {
        return view('livewire.donation.donation-form', ['methods' => PaymentMethod::where('status', 'AKTIF')->get()])->layout('layouts.app', ['title' => 'Donasi']);
    }
}
