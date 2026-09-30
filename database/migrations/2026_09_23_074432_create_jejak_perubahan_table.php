<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('jejak_perubahan', function (Blueprint $table) {
            $table->id();

            // Morph karena Timbangan Sampah akan memakai jejak yang sama persis;
            // keduanya adalah Catatan dengan siklus hidup yang serupa.
            $table->morphs('catatan');

            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('kolom', 50);
            $table->string('nilai_lama', 255)->nullable();
            $table->string('nilai_baru', 255)->nullable();
            $table->timestamp('created_at')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('jejak_perubahan');
    }
};
