<x-layouts.publik>
    @if ($projek === null)
        <main class="flex min-h-[80vh] flex-col bg-botol text-krem">
            <div class="flex items-center justify-between gap-6 px-5 py-6 sm:px-8 lg:px-12 lg:py-10">
                <x-merek />
                <x-navigasi-publik />
            </div>
            <div class="my-auto px-5 pb-16 sm:px-8 lg:px-12">
                <h1 class="max-w-[14ch] font-display text-[clamp(3rem,7vw,8rem)] font-extrabold uppercase leading-[0.9]">
                    Belum ada projek yang berjalan
                </h1>
                <p class="mt-6 max-w-[48ch] text-xl text-krem-lembut">
                    Papan peringkat jelantah akan tampil di sini begitu guru membuka projek baru.
                </p>
            </div>
        </main>
    @else
        @php
            $terbanyak = (float) $papanPeringkat->max('total_liter');
            $isiBatang = fn (float $liter): float => $terbanyak > 0 ? round($liter / $terbanyak, 4) : 0;
            $liter = fn (float $nilai): string => number_format($nilai, 2, ',', '.');

            $tigaTeratas = $papanPeringkat->take(3);
            $sisanya = $papanPeringkat->slice(3)->values();
        @endphp

        <main class="lg:grid lg:min-h-screen lg:grid-cols-[minmax(0,5fr)_minmax(0,7fr)]">
            {{-- Panel kiri: identitas projek dan jumlah keseluruhan. --}}
            <section class="bg-botol text-krem">
                <div class="flex h-full flex-col gap-10 px-5 py-6 sm:px-8 lg:gap-8 lg:px-12 lg:py-9">
                    <div class="flex items-center justify-between gap-6">
                        <x-merek />
                        <x-navigasi-publik class="lg:hidden" />
                    </div>

                    <div>
                        <p class="font-bold text-krem-lembut">
                            Projek berjalan
                            @if ($projek->tahunAjaran)
                                · Tahun ajaran {{ $projek->tahunAjaran->nama }}
                            @endif
                        </p>
                        <h1 class="mt-3 max-w-[12ch] font-display text-[clamp(3rem,4.8vw,6.25rem)] font-extrabold uppercase leading-[0.88] text-balance">
                            {{ $projek->nama }}
                        </h1>
                    </div>

                    <div class="grid grid-cols-[minmax(0,9rem)_minmax(0,1fr)] items-end gap-5 sm:grid-cols-[minmax(0,12rem)_minmax(0,1fr)] sm:gap-8 lg:grid-cols-[minmax(0,11rem)_minmax(0,1fr)]">
                        <x-gelas-takar :liter="$totalLiter" class="w-full" />
                        <div class="pb-2">
                            <p class="font-display text-[clamp(3.25rem,6vw,7.5rem)] font-extrabold leading-[0.85] text-jelantah tabular-nums">
                                {{ $liter($totalLiter) }}<span class="ml-1 text-[0.45em]">L</span>
                            </p>
                            <p class="mt-3 max-w-[22ch] text-lg leading-snug">
                                jelantah dibawa murid dari rumah dan sudah diverifikasi guru
                            </p>
                        </div>
                    </div>

                    <p class="max-w-[30ch] text-xl leading-snug text-krem-lembut">
                        @if ($totalRupiah > 0)
                            Sebanyak {{ number_format($totalKgTerjual, 2, ',', '.') }} kg sudah dijual ke pengepul
                            dan menjadi
                            <strong class="mt-1 block font-display text-[clamp(2.5rem,3.6vw,4.5rem)] font-extrabold leading-none whitespace-nowrap text-krem tabular-nums">Rp {{ number_format($totalRupiah, 0, ',', '.') }}</strong>
                        @else
                            Belum ada jelantah yang dijual. Setelah dijual ke pengepul, hasilnya dalam rupiah tampil di sini.
                        @endif
                    </p>

                    @if ($pengumpulanBerikutnya)
                        <div class="mt-auto self-start -rotate-2 border-[3px] border-jelantah px-5 py-3 text-jelantah outline-2 outline-offset-4 outline-jelantah/50 outline-dashed">
                            <p class="text-sm font-bold uppercase tracking-[0.12em]">
                                {{ $pengumpulanBerikutnya->isToday() ? 'Pengumpulan jelantah' : 'Pengumpulan berikutnya' }}
                            </p>
                            <p class="mt-1 font-stencil text-[clamp(2rem,2.6vw,3rem)] font-extrabold uppercase leading-none">
                                {{ $pengumpulanBerikutnya->isToday() ? 'Hari ini' : $pengumpulanBerikutnya->translatedFormat('l, j F') }}
                            </p>
                        </div>
                    @endif
                </div>
            </section>

            {{-- Panel kanan: papan peringkat kelas. --}}
            <section class="px-5 py-10 sm:px-8 lg:px-12 lg:py-10" aria-labelledby="judul-peringkat">
                <div class="flex items-start justify-between gap-8">
                    <div>
                        <h2 id="judul-peringkat" class="font-display text-[clamp(2.5rem,3.6vw,4.5rem)] font-extrabold uppercase leading-[0.9]">
                            Papan peringkat kelas
                        </h2>
                        <p class="mt-3 max-w-[58ch] text-tinta-lembut">
                            Diurutkan menurut total liter jelantah. Rata-rata per murid hanya keterangan.
                        </p>
                    </div>
                    <x-navigasi-publik class="hidden shrink-0 lg:flex" />
                </div>

                @if ($papanPeringkat->isEmpty())
                    <p class="mt-10 border-2 border-dashed border-garis px-6 py-10 text-center text-lg text-tinta-lembut">
                        Belum ada kelas pada tahun ajaran projek ini.
                    </p>
                @else
                    @if ($terbanyak == 0)
                        <p class="mt-8 bg-kertas-tua px-5 py-4 font-bold">
                            Belum ada setoran yang diverifikasi. Semua kelas masih mulai dari nol.
                        </p>
                    @endif

                    <ol class="mt-8 border-t-[3px] border-tinta">
                        @foreach ($tigaTeratas as $baris)
                            <li class="grid grid-cols-[2.75rem_minmax(0,1fr)_auto] items-center gap-x-4 gap-y-3 border-b border-garis py-4 sm:grid-cols-[3.5rem_minmax(7rem,12rem)_minmax(0,1fr)_auto] sm:gap-x-6">
                                <span class="font-stencil text-5xl font-extrabold leading-none text-jelantah-tua">{{ $loop->iteration }}</span>
                                <span class="font-display text-3xl font-extrabold uppercase leading-none sm:text-4xl">{{ $baris['kelas'] }}</span>
                                <span class="col-span-3 row-start-2 h-4 bg-kertas-tua sm:col-span-1 sm:row-start-auto sm:h-5" aria-hidden="true">
                                    <span class="isi-batang block h-full bg-jelantah"
                                        style="--isi: {{ $isiBatang($baris['total_liter']) }}; --tunda: {{ $loop->index * 90 }}ms"></span>
                                </span>
                                <span class="text-right">
                                    <span class="block font-display text-3xl font-extrabold leading-none tabular-nums sm:text-4xl">{{ $liter($baris['total_liter']) }} L</span>
                                    <span class="text-sm text-tinta-lembut tabular-nums">{{ $liter($baris['rata_rata_liter']) }} L per murid</span>
                                </span>
                            </li>
                        @endforeach
                    </ol>

                    @if ($sisanya->isNotEmpty())
                        <ol start="4" class="grid min-[100rem]:grid-flow-col min-[100rem]:grid-cols-2 min-[100rem]:grid-rows-[repeat(var(--baris),auto)] min-[100rem]:gap-x-10"
                            style="--baris: {{ (int) ceil($sisanya->count() / 2) }}">
                            @foreach ($sisanya as $baris)
                                <li class="grid grid-cols-[2.25rem_minmax(0,6.5rem)_minmax(0,1fr)_auto] items-center gap-x-4 border-b border-garis py-2">
                                    <span class="font-display text-xl font-bold text-tinta-lembut tabular-nums">{{ $loop->iteration + 3 }}</span>
                                    <span class="truncate font-display text-2xl font-bold uppercase leading-none">{{ $baris['kelas'] }}</span>
                                    <span class="h-2.5 bg-kertas-tua" aria-hidden="true">
                                        <span class="isi-batang block h-full bg-jelantah"
                                            style="--isi: {{ $isiBatang($baris['total_liter']) }}; --tunda: {{ 270 + $loop->index * 30 }}ms"></span>
                                    </span>
                                    <span class="text-right leading-tight">
                                        <span class="block font-display text-2xl font-bold tabular-nums">{{ $liter($baris['total_liter']) }} L</span>
                                        <span class="text-xs text-tinta-lembut tabular-nums">{{ $liter($baris['rata_rata_liter']) }} / murid</span>
                                    </span>
                                </li>
                            @endforeach
                        </ol>
                    @endif
                @endif
            </section>
        </main>

        {{--
            Pemilahan Sampah sengaja tampil dengan bahasa visual berbeda dari
            papan peringkat: tanpa urutan, tanpa warna jelantah, tanpa pemenang.
        --}}
        <section class="border-t border-garis lg:grid lg:grid-cols-[minmax(0,5fr)_minmax(0,7fr)]" aria-labelledby="judul-sampah">
            <div class="px-5 pt-14 sm:px-8 lg:px-12 lg:py-20">
                <h2 id="judul-sampah" class="font-display text-[clamp(2.5rem,3.6vw,4.5rem)] font-extrabold uppercase leading-[0.9]">
                    Pemilahan sampah harian
                </h2>
                <p class="mt-5 max-w-[44ch] text-lg leading-relaxed">
                    Setiap sore, sampah dari seluruh tong sekolah dipilah menurut jenisnya lalu ditimbang.
                    Ini bukan lomba: yang dicari adalah garis yang terus menurun.
                </p>
                <p class="mt-8 inline-block border-2 border-dashed border-tinta-lembut px-4 py-2 font-bold text-tinta-lembut">
                    Pencatatan timbangan belum dimulai
                </p>
            </div>

            <figure class="relative px-5 py-14 sm:px-8 lg:px-12 lg:py-20">
                <svg viewBox="0 0 640 260" class="w-full" aria-hidden="true">
                    @foreach ([40, 95, 150, 205] as $y)
                        <line x1="48" x2="630" y1="{{ $y }}" y2="{{ $y }}" class="stroke-garis" stroke-width="1.5" stroke-dasharray="6 8" />
                    @endforeach
                    <line x1="48" x2="48" y1="20" y2="232" class="stroke-tinta-lembut" stroke-width="2" />
                    <line x1="48" x2="630" y1="232" y2="232" class="stroke-tinta-lembut" stroke-width="2" />
                    <text x="0" y="30" class="fill-tinta-lembut text-[15px] font-bold">kg</text>
                    <text x="630" y="256" text-anchor="end" class="fill-tinta-lembut text-[15px] font-bold">hari sekolah</text>
                </svg>
                <figcaption class="absolute inset-0 flex items-center justify-center px-10">
                    <span class="max-w-[34ch] bg-kertas px-5 py-3 text-center text-tinta-lembut">
                        Grafik naik-turunnya sampah per jenis muncul setelah timbangan pertama diverifikasi guru.
                    </span>
                </figcaption>
            </figure>
        </section>
    @endif
</x-layouts.publik>
