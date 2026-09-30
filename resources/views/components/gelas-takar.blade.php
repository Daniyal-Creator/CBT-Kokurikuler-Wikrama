{{--
    Gelas takar yang terisi sesuai liter jelantah terkumpul. Skalanya dibulatkan
    ke angka yang enak dibaca (1, 2, 2,5, 5 × 10ⁿ) sedikit di atas isinya,
    sehingga gelas tidak pernah tampak penuh atau hampir kosong tanpa sebab.
--}}
@props(['liter'])

@php
    $target = max($liter * 1.15, 1);
    $eksponen = 10 ** floor(log10($target));
    $kapasitas = collect([1, 2, 2.5, 5, 10])
        ->map(fn ($pengali) => $pengali * $eksponen)
        ->first(fn ($calon) => $calon >= $target);

    $rasio = min($liter / $kapasitas, 1);

    // Dasar gelas di y=246, garis skala tertinggi di y=60.
    $tinggiSkala = 186;
    $permukaan = 246 - $tinggiSkala * $rasio;

    $formatSkala = fn (float $nilai): string => number_format($nilai, fmod($nilai, 1) == 0 ? 0 : 1, ',', '.');
@endphp

<svg viewBox="0 0 220 260" role="img"
    aria-label="Gelas takar berisi {{ number_format($liter, 2, ',', '.') }} liter dari skala {{ $formatSkala($kapasitas) }} liter"
    {{ $attributes->merge(['class' => 'overflow-visible']) }}>
    <defs>
        <clipPath id="bagian-dalam-takar">
            <path d="M44 22 L62 34 L168 34 L178 236 Q178 246 168 246 L62 246 Q52 246 52 236 L60 46 Z" />
        </clipPath>
    </defs>

    <g clip-path="url(#bagian-dalam-takar)">
        <rect x="40" y="20" width="150" height="230" class="fill-botol-tua" />
        @if ($rasio > 0)
            <g class="isi-takar">
                <rect x="40" y="{{ $permukaan }}" width="150" height="{{ 250 - $permukaan }}" class="fill-jelantah" />
                <rect x="40" y="{{ $permukaan }}" width="150" height="3" class="fill-jelantah-tua" />
            </g>
        @endif
    </g>

    @foreach ([1 / 8, 3 / 8, 5 / 8, 7 / 8] as $pecahan)
        <line x1="62" x2="72" y1="{{ 246 - $tinggiSkala * $pecahan }}" y2="{{ 246 - $tinggiSkala * $pecahan }}"
            class="stroke-krem" stroke-width="2" />
    @endforeach
    @foreach ([1 / 4, 2 / 4, 3 / 4, 1] as $pecahan)
        @php $y = 246 - $tinggiSkala * $pecahan; @endphp
        <line x1="62" x2="84" y1="{{ $y }}" y2="{{ $y }}" class="stroke-krem" stroke-width="3" />
        <text x="90" y="{{ $y + 6 }}" class="fill-krem stroke-botol-tua font-display text-[17px] font-bold"
            stroke-width="4" paint-order="stroke">{{ $formatSkala($kapasitas * $pecahan) }} L</text>
    @endforeach

    <path d="M44 22 L62 34 L168 34 L178 236 Q178 246 168 246 L62 246 Q52 246 52 236 L60 46 Z"
        class="fill-none stroke-krem" stroke-width="6" stroke-linejoin="round" />
    <path d="M173 72 C216 72 216 172 177 178" class="fill-none stroke-krem" stroke-width="7" stroke-linecap="round" />
</svg>
