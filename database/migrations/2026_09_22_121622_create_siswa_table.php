<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('siswa', function (Blueprint $table) {
            $table->id();
            $table->string('nis', 30)->unique();
            $table->string('nama', 120);
            $table->char('jenis_kelamin', 1)->nullable();
            $table->timestamps();

            // Pencarian siswa saat mencatat Setoran dilakukan lewat nama
            // ketika petugas tidak hafal NIS-nya.
            $table->index('nama');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('siswa');
    }
};
