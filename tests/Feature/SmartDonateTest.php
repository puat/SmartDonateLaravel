<?php

use App\Livewire\Campaign\CampaignCreate;
use App\Livewire\Milestone\EvidenceUpload;
use App\Livewire\Milestone\MilestoneManager;
use App\Livewire\Verifier\MilestoneVerification as MilestoneVerificationComponent;
use App\Models\Campaign;
use App\Models\Category;
use App\Models\FundRelease;
use App\Models\Milestone;
use App\Models\MilestoneEvidence;
use App\Models\PaymentMethod;
use App\Models\User;
use App\Services\BlockchainService;
use App\Services\DonationService;
use App\Services\FundReleaseService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use Livewire\Livewire;

uses(RefreshDatabase::class);

function sdUser(string $role = 'DONATUR'): User
{
    return User::factory()->create(['role' => $role, 'status' => 'AKTIF', 'password' => 'password']);
}
function sdCampaign(?User $manager = null, array $data = []): Campaign
{
    $manager ??= sdUser('PENGELOLA');
    $category = Category::create(['name' => 'Sosial', 'slug' => 'sosial-'.uniqid()]);

    return Campaign::create(array_merge(['user_id' => $manager->id, 'category_id' => $category->id, 'title' => 'Program Pengujian Transparan', 'slug' => 'program-'.uniqid(), 'description' => str_repeat('Deskripsi program transparan. ', 3), 'location' => 'Pekanbaru', 'target_amount' => 100000000, 'collected_amount' => 50000000, 'escrow_amount' => 50000000, 'released_amount' => 0, 'start_date' => now(), 'end_date' => now()->addMonth(), 'status' => 'AKTIF', 'verification_status' => 'TERVERIFIKASI'], $data));
}

test('TC-01 user login diarahkan berdasarkan role', function (string $role, string $route) {
    $user = sdUser($role);
    $this->post('/logout');
    $this->actingAs($user)->get(route('dashboard'))->assertRedirect(route($route));
})->with([['DONATUR', 'dashboard.donatur'], ['PENGELOLA', 'dashboard.pengelola'], ['VERIFIER', 'dashboard.verifier'], ['ADMIN', 'dashboard.admin']]);

test('TC-02 pengelola dapat membuat campaign berstatus draft', function () {
    $manager = sdUser('PENGELOLA');
    $category = Category::create(['name' => 'Pendidikan', 'slug' => 'pendidikan']);
    Livewire::actingAs($manager)->test(CampaignCreate::class)->set('title', 'Renovasi Sekolah Transparan')->set('category_id', $category->id)->set('location', 'Kampar')->set('description', str_repeat('Program pendidikan untuk masyarakat. ', 2))->set('target_amount', 150000000)->set('start_date', now()->format('Y-m-d'))->set('end_date', now()->addMonth()->format('Y-m-d'))->call('save')->assertHasNoErrors();
    $this->assertDatabaseHas('campaigns', ['title' => 'Renovasi Sekolah Transparan', 'status' => 'DRAFT', 'verification_status' => 'MENUNGGU']);
});

test('TC-03 donasi disimpan sebagai integer Rupiah', function () {
    $donor = sdUser();
    $campaign = sdCampaign();
    $method = PaymentMethod::create(['name' => 'QRIS', 'type' => 'QRIS', 'status' => 'AKTIF']);
    $donation = app(DonationService::class)->create($donor, $campaign, 500000, $method);
    expect($donation->amount)->toBe(500000)->and($donation->donation_code)->toMatch('/DON-\d{4}-\d{6}/');
});

test('TC-04 pembayaran prototipe ditandai berhasil dan merupakan simulasi', function () {
    $donor = sdUser();
    $campaign = sdCampaign();
    $method = PaymentMethod::create(['name' => 'Virtual Account', 'type' => 'VIRTUAL_ACCOUNT', 'status' => 'AKTIF']);
    $donation = app(DonationService::class)->create($donor, $campaign, 100000, $method);
    expect($donation->payment_status)->toBe('BERHASIL')->and($donation->blockchain_status)->toBe('TERKONFIRMASI');
    $this->assertDatabaseHas('blockchain_transactions', ['donation_id' => $donation->id, 'status' => 'TERKONFIRMASI']);
});

test('TC-05 blockchain service membuat hash heksadesimal simulasi', function () {
    $campaign = sdCampaign();
    $tx = app(BlockchainService::class)->recordCampaign($campaign);
    expect($tx->transaction_hash)->toMatch('/^0x[a-f0-9]{64}$/')->and($tx->metadata['mode'])->toBe('SIMULATION');
});

test('TC-06 milestone dengan bukti dapat diajukan', function () {
    $manager = sdUser('PENGELOLA');
    $campaign = sdCampaign($manager);
    $milestone = Milestone::create(['campaign_id' => $campaign->id, 'title' => 'Tahap 1', 'description' => 'Pelaksanaan awal', 'percentage' => 100, 'amount' => 30000000, 'sequence' => 1, 'status' => 'TERBUKA']);
    MilestoneEvidence::create(['milestone_id' => $milestone->id, 'user_id' => $manager->id, 'title' => 'Nota', 'file_path' => 'evidences/nota.pdf', 'file_name' => 'nota.pdf', 'file_hash' => str_repeat('a', 64), 'uploaded_at' => now()]);
    Livewire::actingAs($manager)->test(MilestoneManager::class, ['campaign' => $campaign])->call('submit', $milestone->id);
    expect($milestone->fresh()->status)->toBe('DALAM_VERIFIKASI');
});

test('TC-06a pengelola dapat menyimpan milestone beserta jejak audit', function () {
    $manager = sdUser('PENGELOLA');
    $campaign = sdCampaign($manager);

    Livewire::actingAs($manager)
        ->test(MilestoneManager::class, ['campaign' => $campaign])
        ->assertSet('sequence', '1')
        ->set('title', 'Tahap Pembangunan')
        ->set('description', 'Pelaksanaan pembangunan tahap pertama.')
        ->set('percentage', '40')
        ->set('amount', '40000000')
        ->call('save')
        ->assertHasNoErrors()
        ->assertSet('sequence', '2');

    $milestone = $campaign->milestones()->sole();
    expect($milestone->title)->toBe('Tahap Pembangunan')
        ->and($milestone->status)->toBe('TERBUKA');
    $this->assertDatabaseHas('blockchain_transactions', ['milestone_id' => $milestone->id, 'activity_type' => 'MILESTONE_CREATED']);
    $this->assertDatabaseHas('audit_logs', ['record_id' => $milestone->id, 'module' => 'MILESTONE', 'action' => 'DIBUAT']);
});

test('TC-06b urutan milestone duplikat ditolak dengan pesan validasi', function () {
    $manager = sdUser('PENGELOLA');
    $campaign = sdCampaign($manager);
    Milestone::create(['campaign_id' => $campaign->id, 'title' => 'Tahap 1', 'description' => 'Tahap pertama', 'percentage' => 30, 'amount' => 30000000, 'sequence' => 1, 'status' => 'TERBUKA']);

    Livewire::actingAs($manager)
        ->test(MilestoneManager::class, ['campaign' => $campaign])
        ->set('title', 'Tahap Duplikat')
        ->set('description', 'Deskripsi tahap duplikat.')
        ->set('percentage', '20')
        ->set('amount', '20000000')
        ->set('sequence', '1')
        ->call('save')
        ->assertHasErrors(['sequence' => 'unique'])
        ->assertSee('Urutan tersebut sudah digunakan pada program ini.');

    expect($campaign->milestones()->count())->toBe(1);
    $this->assertDatabaseMissing('milestones', ['campaign_id' => $campaign->id, 'title' => 'Tahap Duplikat']);
});

test('TC-07 unggah bukti menghasilkan SHA-256', function () {
    Storage::fake('public');
    $manager = sdUser('PENGELOLA');
    $campaign = sdCampaign($manager);
    $milestone = Milestone::create(['campaign_id' => $campaign->id, 'title' => 'Tahap 1', 'description' => 'Tahap pertama', 'percentage' => 30, 'amount' => 30000000, 'sequence' => 1, 'status' => 'TERBUKA']);
    $file = UploadedFile::fake()->createWithContent('bukti.pdf', 'isi bukti akademik');
    Livewire::actingAs($manager)->test(EvidenceUpload::class, ['milestone' => $milestone])->set('title', 'Laporan Belanja')->set('file', $file)->call('save')->assertHasNoErrors();
    $evidence = MilestoneEvidence::first();
    expect($evidence->file_hash)->toBe(hash('sha256', 'isi bukti akademik'))->and(strlen($evidence->file_hash))->toBe(64);
    Storage::disk('public')->assertExists($evidence->file_path);
});

test('TC-08 verifier dapat menyetujui milestone', function () {
    $verifier = sdUser('VERIFIER');
    $m = Milestone::create(['campaign_id' => sdCampaign()->id, 'title' => 'Tahap 1', 'description' => 'Tahap pertama', 'percentage' => 30, 'amount' => 30000000, 'sequence' => 1, 'status' => 'DALAM_VERIFIKASI']);
    MilestoneEvidence::create(['milestone_id' => $m->id, 'user_id' => $m->campaign->user_id, 'title' => 'Bukti', 'file_path' => 'bukti.pdf', 'file_name' => 'bukti.pdf', 'file_hash' => str_repeat('b', 64), 'uploaded_at' => now()]);
    Livewire::actingAs($verifier)->test(MilestoneVerificationComponent::class)->set("notes.{$m->id}", 'Bukti sesuai dan lengkap.')->call('decide', $m->id, 'DISETUJUI')->assertHasNoErrors();
    expect($m->fresh()->status)->toBe('TERVERIFIKASI');
});

test('TC-09 dana milestone terverifikasi dapat dicairkan sekali', function () {
    $admin = sdUser('ADMIN');
    $campaign = sdCampaign();
    $m = Milestone::create(['campaign_id' => $campaign->id, 'title' => 'Tahap 1', 'description' => 'Tahap pertama', 'percentage' => 30, 'amount' => 30000000, 'sequence' => 1, 'status' => 'TERVERIFIKASI']);
    $nextMilestone = Milestone::create(['campaign_id' => $campaign->id, 'title' => 'Tahap 2', 'description' => 'Tahap kedua', 'percentage' => 40, 'amount' => 20000000, 'sequence' => 2, 'status' => 'TERKUNCI']);
    $lastMilestone = Milestone::create(['campaign_id' => $campaign->id, 'title' => 'Tahap 3', 'description' => 'Tahap ketiga', 'percentage' => 30, 'amount' => 20000000, 'sequence' => 3, 'status' => 'TERKUNCI']);
    $release = app(FundReleaseService::class)->release($m, $admin);
    expect($release->status)->toBe('DICAIRKAN')
        ->and($campaign->fresh()->released_amount)->toBe(30000000)
        ->and($campaign->fresh()->escrow_amount)->toBe(20000000)
        ->and($nextMilestone->fresh()->status)->toBe('TERBUKA')
        ->and($lastMilestone->fresh()->status)->toBe('TERKUNCI');
    $this->assertDatabaseHas('audit_logs', ['record_id' => $nextMilestone->id, 'module' => 'MILESTONE', 'action' => 'DIBUKA']);
});

test('TC-10 pencairan duplikat dicegah', function () {
    $admin = sdUser('ADMIN');
    $campaign = sdCampaign();
    $m = Milestone::create(['campaign_id' => $campaign->id, 'title' => 'Tahap 1', 'description' => 'Tahap pertama', 'percentage' => 20, 'amount' => 20000000, 'sequence' => 1, 'status' => 'TERVERIFIKASI']);
    app(FundReleaseService::class)->release($m, $admin);
    $m->update(['status' => 'TERVERIFIKASI']);
    expect(fn () => app(FundReleaseService::class)->release($m, $admin))->toThrow(ValidationException::class);
    expect(FundRelease::where('milestone_id', $m->id)->count())->toBe(1);
});
