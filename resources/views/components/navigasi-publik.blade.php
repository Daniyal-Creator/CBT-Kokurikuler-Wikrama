<nav {{ $attributes->merge(['class' => 'flex items-center gap-5 font-bold']) }} aria-label="Akun">
    @auth
        <a href="{{ route('filament.admin.pages.dashboard') }}"
            class="border-2 border-current px-4 py-2 transition-colors hover:bg-current/10">Buka panel</a>
    @else
        <a href="{{ route('filament.admin.auth.register') }}" class="underline-offset-4 hover:underline">Daftar</a>
        <a href="{{ route('filament.admin.auth.login') }}"
            class="border-2 border-current px-4 py-2 transition-colors hover:bg-current/10">Masuk</a>
    @endauth
</nav>
