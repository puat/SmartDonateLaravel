<div>
    <div class="mb-7 flex flex-wrap items-end justify-between gap-4">
        <div><span class="badge">{{ $campaign->title }}</span>
            <h2 class="mt-4 text-3xl font-extrabold">Kelola Milestone</h2>
        </div>
        <p class="text-sm text-slate-400">Total: <b class="text-white">{{ $milestones->sum('percentage') }}%</b> / 100%</p>
    </div>
    <div class="mb-7 rounded-2xl border border-cyan-400/20 bg-cyan-400/5 p-5 text-sm leading-6 text-slate-300">
        <b class="text-cyan-200">Alur pencairan bertahap:</b>
        unggah bukti pada tahap yang terbuka, ajukan verifikasi, lalu tunggu verifier menyetujui dan mencairkan dana. Setelah dana dicairkan, tahap berikutnya otomatis terbuka.
    </div>
    <div class="grid gap-7 xl:grid-cols-[.75fr_1.25fr]">
        <form wire:submit="save" class="card h-fit">
            <h3 class="text-lg font-bold">Tambah Tahap</h3>
            <div class="mt-5 space-y-4">
                <div><label class="label">Nama tahap</label><input class="input" wire:model="title">@error('title')<p class="error">{{ $message }}</p>@enderror</div>
                <div><label class="label">Deskripsi</label><textarea class="input" wire:model="description"></textarea>@error('description')<p class="error">{{ $message }}</p>@enderror</div>
                <div class="grid grid-cols-2 gap-3">
                    <div><label class="label">Persentase</label><input type="number" class="input" wire:model="percentage">@error('percentage')<p class="error">{{ $message }}</p>@enderror</div>
                    <div><label class="label">Urutan</label><input type="number" min="1" step="1" class="input" wire:model="sequence">@error('sequence')<p class="error">{{ $message }}</p>@enderror</div>
                </div>
                <div><label class="label">Nominal Rupiah</label><input type="number" min="1" step="1" class="input" wire:model="amount" placeholder="Contoh: 10000000">@error('amount')<p class="error">{{ $message }}</p>@enderror</div><button type="submit" class="btn-primary w-full" wire:loading.attr="disabled" wire:target="save"><span wire:loading.remove wire:target="save">Tambah Milestone</span><span wire:loading wire:target="save">Menyimpan...</span></button>
            </div>
        </form>
        <div class="space-y-4">@foreach($milestones as $m)<article wire:key="milestone-{{ $m->id }}" class="card">
                <div class="flex flex-wrap justify-between gap-3">
                    <div class="flex gap-4"><span class="grid size-12 place-items-center rounded-xl bg-emerald-400/10 font-bold text-emerald-300">{{ $m->percentage }}%</span>
                        <div>
                            <h3 class="font-bold">{{ $m->sequence }}. {{ $m->title }}</h3>
                            <p class="mt-1 text-sm text-slate-500">@rupiah($m->amount) · {{ $m->evidences->count() }} bukti</p>
                        </div>
                    </div><span class="badge h-fit">{{ str_replace('_',' ',$m->status) }}</span>
                </div>
                <p class="mt-4 text-sm text-slate-400">{{ $m->description }}</p>
                @if($m->status === 'TERKUNCI')
                    <p class="mt-4 rounded-xl border border-amber-400/15 bg-amber-400/5 px-4 py-3 text-xs leading-5 text-amber-200">Tahap ini akan terbuka otomatis setelah tahap sebelumnya selesai diverifikasi dan dananya dicairkan.</p>
                @elseif($m->status === 'TERVERIFIKASI')
                    <p class="mt-4 rounded-xl border border-emerald-400/15 bg-emerald-400/5 px-4 py-3 text-xs leading-5 text-emerald-200">Bukti telah disetujui. Menunggu verifier mengotorisasi pencairan dana.</p>
                @endif
                <div class="mt-5 flex flex-wrap gap-2"><a class="btn-secondary !py-2 text-sm" href="{{ route('milestone.evidence',$m) }}">Unggah Bukti</a>@if(in_array($m->status,['TERBUKA','DITOLAK']))<button wire:click="submit({{ $m->id }})" class="btn-primary !py-2 text-sm">Ajukan Verifikasi</button>@endif</div>
            </article>@endforeach</div>
    </div>
</div>
