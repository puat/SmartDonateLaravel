<?php

use App\Livewire\Admin\SystemManagement;
use App\Livewire\Auth\ForgotPassword;
use App\Livewire\Auth\Login;
use App\Livewire\Auth\Register;
use App\Livewire\Auth\ResetPassword;
use App\Livewire\Blockchain\BlockchainExplorer;
use App\Livewire\Blockchain\TransactionDetail;
use App\Livewire\Campaign\CampaignCreate;
use App\Livewire\Campaign\CampaignDetail;
use App\Livewire\Campaign\CampaignEdit;
use App\Livewire\Campaign\CampaignList;
use App\Livewire\Dashboard\AdminDashboard;
use App\Livewire\Dashboard\DonorDashboard;
use App\Livewire\Dashboard\ManagerDashboard;
use App\Livewire\Dashboard\VerifierDashboard;
use App\Livewire\Donation\DonationForm;
use App\Livewire\Donation\DonationHistory;
use App\Livewire\Donation\DonationSuccess;
use App\Livewire\Milestone\EvidenceUpload;
use App\Livewire\Milestone\MilestoneManager;
use App\Livewire\Verifier\CampaignVerification;
use App\Livewire\Verifier\MilestoneVerification;
use App\Models\AuditLog;
use App\Models\BlockchainTransaction;
use App\Models\Campaign;
use App\Models\Donation;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    $total = Donation::where('payment_status', 'BERHASIL')->sum('amount');

    return view('welcome', ['stats' => ['Total Donasi' => 'Rp'.number_format($total / 1000000, 1, ',', '.').' jt', 'Jumlah Program' => Campaign::count(), 'Jumlah Donatur' => Donation::distinct('user_id')->count('user_id'), 'Transaksi Tercatat' => BlockchainTransaction::count()]]);
})->name('home');

Route::middleware('guest')->group(function () {
    Route::get('/login', Login::class)->name('login');
    Route::get('/daftar', Register::class)->name('register');
    Route::get('/lupa-kata-sandi', ForgotPassword::class)->name('password.request');
    Route::get('/atur-ulang-kata-sandi/{token}', ResetPassword::class)->name('password.reset');
});
Route::post('/logout', function () {
    Auth::logout();
    request()->session()->invalidate();
    request()->session()->regenerateToken();

    return redirect()->route('home');
})->middleware('auth')->name('logout');

Route::get('/campaign', CampaignList::class)->name('campaign.index');
Route::get('/campaign/{campaign}', CampaignDetail::class)->name('campaign.show');
Route::get('/blockchain', BlockchainExplorer::class)->name('blockchain.index');
Route::get('/blockchain/{transactionHash}', TransactionDetail::class)->name('blockchain.show');
Route::get('/audit', fn () => view('pages.audit', ['logs' => AuditLog::with('user')->latest()->paginate(20)]))->name('audit');

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', function () {
        return redirect()->route(match (auth()->user()->role) {
            'PENGELOLA' => 'dashboard.pengelola','VERIFIER' => 'dashboard.verifier','ADMIN' => 'dashboard.admin',default => 'dashboard.donatur'
        });
    })->name('dashboard');
    Route::get('/dashboard/donatur', DonorDashboard::class)->middleware('role:DONATUR,ADMIN')->name('dashboard.donatur');
    Route::get('/dashboard/pengelola', ManagerDashboard::class)->middleware('role:PENGELOLA,ADMIN')->name('dashboard.pengelola');
    Route::get('/dashboard/verifier', VerifierDashboard::class)->middleware('role:VERIFIER,ADMIN')->name('dashboard.verifier');
    Route::get('/dashboard/admin', AdminDashboard::class)->middleware('role:ADMIN')->name('dashboard.admin');
    Route::get('/admin/sistem', SystemManagement::class)->middleware('role:ADMIN')->name('admin.system');
    Route::get('/donasi/{campaign}', DonationForm::class)->middleware('role:DONATUR,ADMIN')->name('donasi.create');
    Route::get('/donasi/sukses/{donation}', DonationSuccess::class)->middleware('role:DONATUR,ADMIN')->name('donasi.sukses');
    Route::get('/riwayat-donasi', DonationHistory::class)->middleware('role:DONATUR,ADMIN')->name('donasi.riwayat');
    Route::get('/pengelola/program/buat', CampaignCreate::class)->middleware('role:PENGELOLA,ADMIN')->name('campaign.create');
    Route::get('/pengelola/program/{campaign}/edit', CampaignEdit::class)->middleware('role:PENGELOLA,ADMIN')->name('campaign.edit');
    Route::get('/pengelola/program/{campaign}/milestone', MilestoneManager::class)->middleware('role:PENGELOLA,ADMIN')->name('milestone.manage');
    Route::get('/pengelola/milestone/{milestone}/bukti', EvidenceUpload::class)->middleware('role:PENGELOLA,ADMIN')->name('milestone.evidence');
    Route::get('/verifier/program', CampaignVerification::class)->middleware('role:VERIFIER,ADMIN')->name('verifier.campaign');
    Route::get('/verifier/milestone', MilestoneVerification::class)->middleware('role:VERIFIER,ADMIN')->name('verifier.milestone');
});
