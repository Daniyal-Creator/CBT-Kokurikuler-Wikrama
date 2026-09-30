<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('projek', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tahun_ajaran_id')->constrained('tahun_ajaran')->cascadeOnDelete();
            $table->string('nama', 150);
            $table->date('tanggal_mulai');
            $table->date('tanggal_selesai');

            // Hari pengumpulan jelantah dalam penomoran ISO-8601 (1 = Senin).
            // Nilainya hanya untuk ditampilkan; sistem tidak menolak setoran
            // bertanggal lain, karena kegiatan pengganti adalah hal biasa.
            $table->unsignedTinyInteger('hari_pengumpulan')->nullable();

            // Jelantah dicatat dalam liter, tetapi dijual dalam kilogram.
            // Faktor ini tinggal di sini, bukan di dalam kode, agar sekolah
            // dapat mengoreksinya tanpa menunggu siapa pun mengubah program.
            $table->decimal('faktor_konversi_liter_ke_kg', 4, 2)->default(0.90);

            $table->boolean('aktif')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('projek');
    }
};
