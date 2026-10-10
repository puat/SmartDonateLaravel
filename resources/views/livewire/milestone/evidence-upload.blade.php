<div class="max-w-5xl">
    <div class="mb-7"><span class="badge">{{ $milestone->campaign->title }}</span>
        <h2 class="mt-4 text-3xl font-extrabold">Bukti {{ $milestone->title }}</h2>
    </div>
    <div class="grid gap-7 lg:grid-cols-[.8fr_1.2fr]">
        <form wire:submit="save" class="card h-fit">
            <div class="space-y-5">
                <div><label class="label">Judul bukti</label><input class="input" wire:model="title">@error('title')<p class="error">{{ $message }}</p>@enderror</div>
                <div><label class="label">Deskripsi</label><textarea class="input" wire:model="description"></textarea></div>
                <div><label class="label">Berkas PDF/JPG/PNG</label><input class="input" type="file" wire:model="file" accept=".pdf,.jpg,.jpeg,.png,application/pdf,image/jpeg,image/png">@error('file')<p class="error">{{ $message }}</p>@enderror<p wire:loading wire:target="file" class="mt-2 text-sm text-cyan-300">Sedang mengunggah berkas...</p></div>
            </div><button type="submit" class="btn-primary mt-6 w-full" wire:loading.attr="disabled"><span wire:loading.remove wire:target="save">Unggah & Buat Hash</span><span wire:loading wire:target="save">Menyimpan...</span></button>
        </form>
        <div class="space-y-4">@forelse($evidences as $e)<article class="card">
                <div class="flex justify-between">
                    <h3 class="font-bold">{{ $e->title }}</h3><span class="badge">MENUNGGU VERIFIKASI</span>
                </div>
                <p class="mt-2 text-sm text-slate-500">{{ $e->file_name }} · {{ $e->uploaded_at->translatedFormat('d M Y H:i') }}</p>
                <p class="mt-4 text-xs text-slate-500">SHA-256 Hash</p><code class="mt-2 block break-all rounded-lg bg-black/20 p-3 text-xs text-emerald-300">{{ $e->file_hash }}</code><a target="_blank" class="mt-3 inline-block text-sm text-cyan-300" href="{{ Storage::url($e->file_path) }}">Lihat berkas ↗</a>
            </article>@empty<div class="card text-slate-500">Belum ada bukti diunggah.</div>@endforelse</div>
    </div>
</div>
