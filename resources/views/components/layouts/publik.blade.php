@props(['judul' => null])

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="publik">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="oklch(0.36 0.072 158)">
    <title>{{ $judul ? $judul.' · '.config('app.name') : config('app.name') }}</title>

    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @fonts
        @vite(['resources/css/app.css'])
    @endif
</head>
<body class="min-h-screen bg-kertas font-sans text-tinta antialiased">
    {{ $slot }}

    <footer class="border-t border-garis bg-kertas-tua">
        <div class="flex flex-col gap-2 px-5 py-8 text-sm text-tinta-lembut sm:px-8 lg:flex-row lg:items-baseline lg:justify-between lg:px-12">
            <p class="max-w-[62ch]">
                Halaman ini hanya menampilkan jumlah keseluruhan. Nama murid tidak pernah ditampilkan,
                dan setiap angka berasal dari catatan yang sudah diverifikasi guru.
            </p>
            <p class="font-bold text-tinta">Kokurikuler P5 · SMK Wikrama</p>
        </div>
    </footer>
</body>
</html>
