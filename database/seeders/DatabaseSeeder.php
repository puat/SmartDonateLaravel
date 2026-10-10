<?php

namespace Database\Seeders;

use App\Models\BlockchainTransaction;
use App\Models\Campaign;
use App\Models\Category;
use App\Models\Donation;
use App\Models\Milestone;
use App\Models\PaymentMethod;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $users = collect([
            ['name' => 'Dina Donatur', 'email' => 'donatur@smartdonate.id', 'role' => 'DONATUR'],
            ['name' => 'Rizky Pengelola', 'email' => 'pengelola@smartdonate.id', 'role' => 'PENGELOLA'],
            ['name' => 'Vera Verifier', 'email' => 'verifier@smartdonate.id', 'role' => 'VERIFIER'],
            ['name' => 'Admin SmartDonate', 'email' => 'admin@smartdonate.id', 'role' => 'ADMIN'],
        ])->mapWithKeys(fn ($data) => [$data['role'] => User::create($data + ['password' => Hash::make('password'), 'phone' => '081234567890', 'status' => 'AKTIF', 'email_verified_at' => now()])]);

        $categories = collect(['Bencana Alam', 'Kesehatan', 'Pendidikan', 'Sosial', 'Lingkungan', 'Keagamaan'])->mapWithKeys(fn ($name) => [$name => Category::create(['name' => $name, 'slug' => Str::slug($name), 'description' => "Program crowdfunding kategori {$name} yang transparan dan terverifikasi."])]);
        $qris = PaymentMethod::create(['name' => 'QRIS', 'type' => 'QRIS', 'account_name' => 'SmartDonate', 'status' => 'AKTIF']);
        foreach (['Bank Syariah Indonesia', 'Bank Mandiri', 'Bank BRI', 'Bank BNI', 'Bank BCA'] as $i => $bank) {
            PaymentMethod::create(['name' => "Transfer {$bank}", 'type' => 'BANK_TRANSFER', 'bank_name' => $bank, 'account_number' => '90000'.($i + 1), 'account_name' => 'SmartDonate', 'status' => 'AKTIF']);
        }
        PaymentMethod::create(['name' => 'Virtual Account', 'type' => 'VIRTUAL_ACCOUNT', 'account_name' => 'SmartDonate', 'status' => 'AKTIF']);

        $items = [
            ['Bantuan Pemulihan Korban Banjir Riau', 'Bencana Alam', 'Riau', 250000000, 187500000],
            ['Renovasi Sekolah di Daerah Terpencil', 'Pendidikan', 'Kampar, Riau', 150000000, 112500000],
            ['Bantuan Operasi Anak Keluarga Prasejahtera', 'Kesehatan', 'Pekanbaru', 75000000, 75000000],
            ['Pembangunan Sumur Air Bersih untuk Masyarakat Desa', 'Sosial', 'Kampar, Riau', 120000000, 84000000],
            ['Beasiswa Pendidikan Anak Yatim dan Dhuafa', 'Pendidikan', 'Indonesia', 200000000, 136000000],
            ['Bantuan Peralatan Medis Daerah Terpencil', 'Kesehatan', 'Kepulauan Riau', 300000000, 210000000],
        ];
        foreach ($items as $index => [$title,$category,$location,$target,$collected]) {
            $released = $index === 0 ? 56250000 : 0;
            $campaign = Campaign::create(['user_id' => $users['PENGELOLA']->id, 'category_id' => $categories[$category]->id, 'title' => $title, 'slug' => Str::slug($title), 'description' => "Program ini menghimpun bantuan masyarakat secara transparan untuk {$title}. Setiap penggunaan dana dibagi dalam milestone, diperiksa verifier, dan dicatat dalam jejak audit blockchain simulasi.", 'location' => $location, 'target_amount' => $target, 'collected_amount' => $collected, 'released_amount' => $released, 'escrow_amount' => $collected - $released, 'start_date' => now()->subDays(30), 'end_date' => now()->addDays(75 + $index * 10), 'status' => 'AKTIF', 'verification_status' => 'TERVERIFIKASI']);
            foreach ([30, 40, 30] as $sequence => $percentage) {
                Milestone::create(['campaign_id' => $campaign->id, 'title' => 'Tahap '.($sequence + 1), 'description' => 'Pelaksanaan dan pelaporan tahap '.($sequence + 1).'.', 'percentage' => $percentage, 'amount' => intdiv($target * $percentage, 100), 'sequence' => $sequence + 1, 'status' => $index === 0 ? (['DICAIRKAN', 'DALAM_VERIFIKASI', 'TERKUNCI'][$sequence]) : ($sequence === 0 ? 'TERBUKA' : 'TERKUNCI'), 'released_at' => $index === 0 && $sequence === 0 ? now()->subDays(3) : null]);
            }
            BlockchainTransaction::create(['transaction_hash' => '0x'.bin2hex(random_bytes(32)), 'block_number' => 19000000 + $index, 'campaign_id' => $campaign->id, 'activity_type' => 'CAMPAIGN_CREATED', 'amount' => 0, 'status' => 'TERKONFIRMASI', 'timestamp' => now()->subDays(30 - $index), 'metadata' => ['mode' => 'SIMULATION', 'catatan' => 'Data contoh akademik']]);
        }
        $first = Campaign::first();
        for ($i = 1; $i <= 428; $i++) {
            Donation::create(['donation_code' => 'DON-'.now()->year.'-'.str_pad((string) $i, 6, '0', STR_PAD_LEFT), 'campaign_id' => $first->id, 'user_id' => $users['DONATUR']->id, 'amount' => $i === 428 ? 16700000 : 400000, 'payment_method_id' => $qris->id, 'payment_status' => 'BERHASIL', 'blockchain_status' => 'TERKONFIRMASI', 'transaction_hash' => '0x'.bin2hex(random_bytes(32)), 'block_number' => 19001000 + $i, 'donated_at' => now()->subMinutes($i)]);
        }
    }
}
