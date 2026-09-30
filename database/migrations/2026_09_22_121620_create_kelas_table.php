<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('kelas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tahun_ajaran_id')->constrained('tahun_ajaran')->cascadeOnDelete();
            $table->string('nama', 30);
            $table->string('tingkat', 10)->nullable();
            $table->timestamps();

            // Sebuah rombongan belajar bernama "8A" hanya boleh ada satu kali
            // dalam satu Tahun Ajaran, tetapi "8A" tahun berikutnya adalah kelas
            // yang berbeda dengan murid yang berbeda.
            $table->unique(['tahun_ajaran_id', 'nama']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kelas');
    }
};
