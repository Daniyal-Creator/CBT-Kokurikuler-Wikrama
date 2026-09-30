<?php

use App\Enums\StatusCatatan;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('setoran', function (Blueprint $table) {
            $table->id();
            $table->foreignId('projek_id')->constrained('projek')->cascadeOnDelete();
            $table->foreignId('siswa_id')->constrained('siswa')->cascadeOnDelete();
            $table->date('tanggal');
            $table->decimal('jumlah_liter', 8, 2);
            $table->string('status', 20)->default(StatusCatatan::Diajukan->value);

            // Siapa yang mengetik. Untuk sekarang selalu Guru atau Admin;
            // kolom untuk Petugas ditambahkan saat login Petugas dibangun.
            $table->foreignId('dicatat_oleh_user_id')->nullable()->constrained('users')->nullOnDelete();

            $table->foreignId('diverifikasi_oleh_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('diverifikasi_pada')->nullable();
            $table->string('alasan_penolakan', 255)->nullable();
            $table->timestamps();

            // Verifikasi dilakukan borongan per tanggal, dan rekap selalu
            // dibatasi satu projek — dua pola kueri yang sama-sama dilayani
            // indeks ini.
            $table->index(['projek_id', 'tanggal']);
            $table->index(['projek_id', 'status']);

            // Sengaja TIDAK ada batasan unik pada (siswa, tanggal): seorang
            // murid boleh menyetor lebih dari sekali dalam sehari, karena tiap
            // baris adalah peristiwa nyata seseorang menyerahkan botol.
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('setoran');
    }
};
