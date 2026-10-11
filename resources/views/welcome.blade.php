<x-layouts.app title="Crowdfunding Transparan">
    <section class="relative overflow-hidden border-b border-white/5">
        <div class="absolute inset-x-0 top-0 -z-10 h-[34rem] bg-[radial-gradient(circle_at_20%_20%,rgba(16,185,129,.13),transparent_48%),radial-gradient(circle_at_85%_15%,rgba(34,211,238,.08),transparent_42%)]"></div>

        <div class="container-app grid min-h-[calc(100vh-4.5rem)] items-center gap-14 py-16 lg:grid-cols-[.92fr_1.08fr] lg:py-20">
            <div class="relative z-10">
                <span class="badge mb-6 gap-2">
                    <span class="size-1.5 rounded-full bg-emerald-300 shadow-[0_0_12px_rgba(110,231,183,.9)]"></span>
                    Transparansi dari donasi hingga pencairan
                </span>

                <h1 class="max-w-3xl text-5xl font-extrabold leading-[1.03] tracking-[-0.045em] text-white sm:text-6xl xl:text-7xl">
                    Kebaikan yang terasa.
                    <span class="mt-2 block text-emerald-400">Dana yang terjaga.</span>
                </h1>

                <p class="mt-7 max-w-xl text-base leading-8 text-slate-300 sm:text-lg">
                    SmartDonate menghubungkan donatur dengan program sosial terverifikasi. Setiap donasi, bukti penggunaan dana, dan pencairan milestone dapat ditelusuri dalam satu alur yang jelas.
                </p>

                <div class="mt-9 flex flex-wrap gap-3">
                    <a class="btn-primary gap-2" href="{{ route('campaign.index') }}">
                        Mulai berdonasi <span aria-hidden="true">→</span>
                    </a>
                    <a class="btn-secondary" href="#cara-kerja">Pelajari cara kerja</a>
                </div>

                <div class="mt-10 flex flex-wrap gap-x-7 gap-y-3 text-xs font-semibold text-slate-400">
                    <span class="flex items-center gap-2"><span class="text-emerald-400">✓</span> Program terverifikasi</span>
                    <span class="flex items-center gap-2"><span class="text-emerald-400">✓</span> Donasi tetap Rupiah</span>
                    <span class="flex items-center gap-2"><span class="text-emerald-400">✓</span> Jejak audit terbuka</span>
                </div>
            </div>

            <div class="relative mx-auto w-full max-w-2xl lg:mx-0">
                <div class="absolute -inset-8 -z-10 rounded-full bg-emerald-400/10 blur-3xl"></div>
                <figure class="group relative overflow-hidden rounded-[2rem] border border-white/15 bg-slate-900 shadow-2xl shadow-black/40">
                    <img
                        src="{{ asset('images/smartdonate-impact.webp') }}"
                        alt="Relawan menyalurkan paket bantuan kepada warga terdampak banjir di Riau"
                        class="h-[30rem] w-full object-cover object-center transition duration-700 group-hover:scale-[1.02] sm:h-[36rem]"
                        width="1536"
                        height="1024"
                        loading="eager"
                        fetchpriority="high"
                    >
                    <div class="absolute inset-0 bg-gradient-to-t from-[#07111f] via-[#07111f]/10 to-transparent"></div>

                    <div class="absolute left-5 top-5 flex items-center gap-2 rounded-full border border-white/15 bg-[#07111f]/75 px-4 py-2 text-xs font-bold text-white backdrop-blur-md">
                        <span class="size-2 rounded-full bg-emerald-400"></span>
                        Dampak program terpantau
                    </div>

                    <figcaption class="absolute inset-x-0 bottom-0 p-6 sm:p-8">
                        <p class="text-xs font-bold uppercase tracking-[.18em] text-emerald-300">Cerita dampak</p>
                        <h2 class="mt-2 max-w-lg text-2xl font-bold leading-tight text-white sm:text-3xl">Bantuan hadir tepat sasaran, laporan tersedia untuk semua.</h2>
                        <p class="mt-3 max-w-xl text-sm leading-6 text-slate-300">Penyaluran bantuan dicatat bersama bukti, milestone, dan status verifikasi agar kepercayaan tumbuh dari data yang dapat diperiksa.</p>
                    </figcaption>
                </figure>

                <div class="absolute -bottom-5 -left-5 hidden w-60 rounded-2xl border border-white/15 bg-[#0b1928]/95 p-4 shadow-2xl backdrop-blur-xl sm:block xl:-left-10">
                    <div class="flex items-center justify-between">
                        <span class="text-xs text-slate-400">Status penyaluran</span>
                        <span class="size-8 rounded-full bg-emerald-400/15 text-center text-lg leading-8 text-emerald-300">✓</span>
                    </div>
                    <p class="mt-3 font-bold text-white">Milestone terverifikasi</p>
                    <div class="mt-3 h-1.5 overflow-hidden rounded-full bg-white/10"><div class="h-full w-3/4 rounded-full bg-gradient-to-r from-emerald-400 to-cyan-400"></div></div>
                    <div class="mt-2 flex justify-between text-[11px] text-slate-500"><span>Tahap 3 dari 4</span><span>75%</span></div>
                </div>
            </div>
        </div>
    </section>

    <section aria-label="Statistik SmartDonate" class="border-b border-white/5 bg-white/[0.025]">
        <div class="container-app grid grid-cols-2 gap-px py-8 sm:grid-cols-4">
            @foreach($stats as $label => $value)
                <div class="border-white/10 px-4 py-3 sm:not-last:border-r sm:px-7">
                    <b class="block text-2xl font-extrabold tracking-tight text-white sm:text-3xl">{{ $value }}</b>
                    <span class="mt-1 block text-xs font-medium text-slate-500">{{ $label }}</span>
                </div>
            @endforeach
        </div>
    </section>

    <section id="cara-kerja" class="container-app py-20 sm:py-28">
        <div class="grid gap-12 lg:grid-cols-[.7fr_1.3fr] lg:gap-20">
            <div>
                <span class="badge">Cara kerja</span>
                <h2 class="mt-5 text-3xl font-extrabold tracking-tight text-white sm:text-4xl">Satu donasi, alur yang mudah dipahami.</h2>
                <p class="mt-5 leading-7 text-slate-400">Bukan sekadar menampilkan total dana. SmartDonate memperlihatkan apa yang terjadi setelah donasi diterima hingga manfaatnya sampai kepada penerima.</p>
                <a href="{{ route('blockchain.index') }}" class="mt-7 inline-flex items-center gap-2 text-sm font-bold text-emerald-300 hover:text-emerald-200">Lihat explorer transparansi <span aria-hidden="true">↗</span></a>
            </div>

            <div class="grid gap-4 md:grid-cols-3">
                @foreach([
                    ['01', 'Donasi tercatat', 'Pembayaran dalam Rupiah tercatat otomatis dan terhubung ke program yang dipilih.'],
                    ['02', 'Bukti diverifikasi', 'Pengelola melampirkan laporan dan bukti SHA-256 pada setiap milestone program.'],
                    ['03', 'Dana dicairkan', 'Dana dilepas bertahap setelah verifier menyetujui progres dan kelengkapan bukti.'],
                ] as $card)
                    <article class="card group relative overflow-hidden !p-6 transition duration-300 hover:-translate-y-1 hover:border-emerald-400/25">
                        <span class="text-5xl font-black text-white/[0.06] transition group-hover:text-emerald-400/10">{{ $card[0] }}</span>
                        <div class="mt-8 size-9 rounded-xl border border-emerald-400/20 bg-emerald-400/10 text-center text-lg font-bold leading-9 text-emerald-300">{{ $loop->iteration }}</div>
                        <h3 class="mt-5 text-lg font-bold text-white">{{ $card[1] }}</h3>
                        <p class="mt-3 text-sm leading-6 text-slate-400">{{ $card[2] }}</p>
                    </article>
                @endforeach
            </div>
        </div>
    </section>

    <section id="tentang" class="border-y border-white/5 bg-[#0a1625]">
        <div class="container-app grid gap-8 py-20 lg:grid-cols-3 lg:py-24">
            <div class="lg:col-span-1">
                <p class="text-xs font-bold uppercase tracking-[.2em] text-cyan-300">Kepercayaan dibangun bersama</p>
                <h2 class="mt-4 text-3xl font-extrabold tracking-tight text-white">Informasi yang tepat untuk setiap peran.</h2>
            </div>

            <div class="grid gap-4 sm:grid-cols-3 lg:col-span-2">
                @foreach([
                    ['Untuk Donatur', 'Pantau progres, penggunaan dana, dan status pencairan tanpa menunggu laporan terpisah.'],
                    ['Untuk Pengelola', 'Kelola program, milestone, dan bukti penggunaan dana dalam satu ruang kerja terstruktur.'],
                    ['Untuk Verifier', 'Periksa bukti dan histori aktivitas sebelum menyetujui setiap tahap pencairan.'],
                ] as $audience)
                    <article class="rounded-2xl border border-white/10 bg-white/[0.035] p-6">
                        <span class="mb-5 block h-px w-10 bg-emerald-400"></span>
                        <h3 class="font-bold text-white">{{ $audience[0] }}</h3>
                        <p class="mt-3 text-sm leading-6 text-slate-400">{{ $audience[1] }}</p>
                    </article>
                @endforeach
            </div>
        </div>
    </section>

    <section class="container-app py-20 sm:py-28">
        <div class="relative overflow-hidden rounded-[2rem] border border-emerald-400/20 bg-gradient-to-br from-emerald-400/15 via-cyan-400/5 to-transparent px-6 py-12 text-center sm:px-12 sm:py-16">
            <div class="absolute left-1/2 top-0 -z-10 size-80 -translate-x-1/2 rounded-full bg-emerald-400/10 blur-3xl"></div>
            <span class="badge">Mulai dari satu langkah</span>
            <h2 class="mx-auto mt-5 max-w-2xl text-3xl font-extrabold tracking-tight text-white sm:text-4xl">Pilih program yang berarti dan ikuti setiap dampaknya.</h2>
            <p class="mx-auto mt-4 max-w-xl leading-7 text-slate-400">Temukan program terverifikasi, berdonasi dalam Rupiah, dan pantau perjalanan dana secara transparan.</p>
            <a href="{{ route('campaign.index') }}" class="btn-primary mt-8 gap-2">Jelajahi program <span aria-hidden="true">→</span></a>
        </div>
    </section>
</x-layouts.app>
