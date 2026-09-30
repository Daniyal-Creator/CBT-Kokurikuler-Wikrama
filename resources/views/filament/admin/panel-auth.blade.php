{{--
    Panel hijau di samping formulir masuk dan daftar. Isinya menjelaskan untuk
    siapa panel ini dan, pada pendaftaran, bahwa akun baru harus disetujui Admin
    lebih dulu: hal yang sebelumnya baru diketahui setelah formulir dikirim.
--}}
@php
    $sedangMendaftar = request()->routeIs('filament.admin.auth.register');
@endphp

<aside class="kw-auth-panel flex flex-col gap-10 bg-botol px-6 py-6 text-krem sm:px-10 lg:min-h-dvh lg:w-[42%] lg:shrink-0 lg:px-12 lg:py-10">
    <x-merek class="self-start" />

    @if ($sedangMendaftar)
        <p class="-mt-4 max-w-[46ch] text-krem-lembut lg:hidden">
            Akun baru perlu disetujui Admin sebelum dapat dipakai masuk.
        </p>
    @endif

    <div class="hidden lg:my-auto lg:block">
        @if ($sedangMendaftar)
            <p class="font-bold text-krem-lembut">Pendaftaran guru</p>
            <p class="mt-3 max-w-[13ch] font-display text-[clamp(3rem,4.4vw,5.5rem)] font-extrabold uppercase leading-[0.9]">
                Bantu jaga angkanya tetap jujur
            </p>
            <ol class="mt-10 grid max-w-[40ch] gap-5 text-lg leading-snug">
                <li class="grid grid-cols-[2.5rem_1fr] items-baseline">
                    <span class="font-stencil text-3xl font-extrabold text-jelantah">1</span>
                    <span>Isi formulir di samping dengan email sekolah Anda.</span>
                </li>
                <li class="grid grid-cols-[2.5rem_1fr] items-baseline">
                    <span class="font-stencil text-3xl font-extrabold text-jelantah">2</span>
                    <span>Admin memeriksa dan menyetujui akun. Sebelum disetujui, akun belum dapat dipakai masuk.</span>
                </li>
                <li class="grid grid-cols-[2.5rem_1fr] items-baseline">
                    <span class="font-stencil text-3xl font-extrabold text-jelantah">3</span>
                    <span>Masuk, lalu verifikasi setoran jelantah dan timbangan sampah.</span>
                </li>
            </ol>
        @else
            <p class="font-bold text-krem-lembut">Panel guru dan admin</p>
            <p class="mt-3 max-w-[13ch] font-display text-[clamp(3rem,4.4vw,5.5rem)] font-extrabold uppercase leading-[0.9]">
                Setiap liter dihitung setelah diverifikasi
            </p>
            <p class="mt-8 max-w-[38ch] text-lg leading-snug text-krem-lembut">
                Verifikasi catatan petugas, catat penjualan jelantah ke pengepul, dan kelola data kelas.
            </p>
        @endif
    </div>

    <a href="{{ route('beranda') }}" class="hidden self-start font-bold text-krem-lembut underline-offset-4 hover:text-krem hover:underline lg:inline">
        Lihat papan peringkat
    </a>
</aside>
