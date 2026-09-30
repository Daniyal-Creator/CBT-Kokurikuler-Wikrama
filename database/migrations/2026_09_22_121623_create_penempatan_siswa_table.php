<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('penempatan_siswa', function (Blueprint $table) {
            $table->id();
            $table->foreignId('siswa_id')->constrained('siswa')->cascadeOnDelete();
            $table->foreignId('kelas_id')->constrained('kelas')->cascadeOnDelete();

            // Disalin dari kelas.tahun_ajaran_id semata-mata agar aturan
            // "satu siswa hanya menempati satu kelas per tahun ajaran" dapat
            // dijaga oleh basis data, bukan hanya oleh kode aplikasi.
            // Pengisiannya otomatis di model; jangan pernah diisi manual.
            $table->foreignId('tahun_ajaran_id')->constrained('tahun_ajaran')->cascadeOnDelete();

            $table->timestamps();

            $table->unique(['siswa_id', 'tahun_ajaran_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('penempatan_siswa');
    }
};
