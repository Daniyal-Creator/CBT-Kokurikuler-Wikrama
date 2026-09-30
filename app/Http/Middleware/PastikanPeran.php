<?php

namespace App\Http\Middleware;

use App\Enums\PeranPengguna;
use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Membatasi rute pada peran tertentu: `peran:admin` atau `peran:admin,guru`.
 *
 * Akun yang belum disetujui Admin selalu ditolak, apa pun perannya — peran
 * yang tercatat pada akun semacam itu belum dapat dipercaya.
 */
class PastikanPeran
{
    public function handle(Request $request, Closure $next, string ...$peranDiizinkan): Response
    {
        $user = $request->user();

        if (! $user instanceof User || ! $user->sudahDisetujui()) {
            abort(Response::HTTP_FORBIDDEN);
        }

        $diizinkan = array_map(
            fn (string $peran): PeranPengguna => PeranPengguna::from($peran),
            $peranDiizinkan,
        );

        if (! in_array($user->peran, $diizinkan, true)) {
            abort(Response::HTTP_FORBIDDEN);
        }

        return $next($request);
    }
}
