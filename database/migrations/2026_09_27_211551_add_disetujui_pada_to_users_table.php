<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Pendaftaran terbuka, tetapi akun yang mendaftar sendiri tidak
            // boleh langsung memverifikasi catatan. Kosong berarti menunggu
            // persetujuan Admin.
            $table->timestamp('disetujui_pada')->nullable()->after('peran');
        });

        // Akun yang sudah ada dibuat oleh Admin, bukan lewat pendaftaran —
        // tanpa baris ini, semua orang terkunci dari panel sesudah migrasi.
        DB::table('users')->update(['disetujui_pada' => now()]);
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('disetujui_pada');
        });
    }
};
