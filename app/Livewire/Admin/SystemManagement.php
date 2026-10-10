<?php

namespace App\Livewire\Admin;

use App\Models\Category;
use App\Models\PaymentMethod;
use App\Models\User;
use App\Services\AuditService;
use Illuminate\Support\Str;
use Livewire\Component;

class SystemManagement extends Component
{
    public string $categoryName = '';

    public string $categoryDescription = '';

    public string $paymentName = '';

    public string $paymentType = 'QRIS';

    public function updateUser(int $id, string $field, string $value): void
    {
        abort_unless(in_array($field, ['role', 'status'], true), 422);
        $allowed = $field === 'role' ? ['DONATUR', 'PENGELOLA', 'VERIFIER', 'ADMIN'] : ['AKTIF', 'NONAKTIF'];
        abort_unless(in_array($value, $allowed, true), 422);
        abort_if($id === auth()->id() && $field === 'status' && $value === 'NONAKTIF', 422, 'Akun sendiri tidak dapat dinonaktifkan.');
        User::findOrFail($id)->update([$field => $value]);
        app(AuditService::class)->record('DIPERBARUI', 'PENGGUNA', $id, "Administrator mengubah {$field} pengguna menjadi {$value}.");
    }

    public function addCategory(): void
    {
        $data = $this->validate(['categoryName' => 'required|min:3|max:100|unique:categories,name', 'categoryDescription' => 'nullable|max:500'], ['categoryName.required' => 'Nama kategori wajib diisi.', 'categoryName.unique' => 'Kategori sudah tersedia.']);
        $category = Category::create(['name' => $data['categoryName'], 'slug' => Str::slug($data['categoryName']).'-'.Str::lower(Str::random(4)), 'description' => $data['categoryDescription']]);
        app(AuditService::class)->record('DIBUAT', 'KATEGORI', $category->id, "Kategori {$category->name} dibuat.");
        $this->reset('categoryName', 'categoryDescription');
    }

    public function addPayment(): void
    {
        $data = $this->validate(['paymentName' => 'required|min:3|max:100', 'paymentType' => 'required|in:QRIS,BANK_TRANSFER,VIRTUAL_ACCOUNT']);
        PaymentMethod::create(['name' => $data['paymentName'], 'type' => $data['paymentType'], 'status' => 'AKTIF']);
        $this->reset('paymentName');
    }

    public function togglePayment(int $id): void
    {
        $method = PaymentMethod::findOrFail($id);
        $method->update(['status' => $method->status === 'AKTIF' ? 'NONAKTIF' : 'AKTIF']);
    }

    public function render()
    {
        return view('livewire.admin.system-management', ['users' => User::latest()->get(), 'categories' => Category::withCount('campaigns')->orderBy('name')->get(), 'methods' => PaymentMethod::orderBy('name')->get()])->layout('layouts.dashboard', ['title' => 'Kelola Sistem']);
    }
}
