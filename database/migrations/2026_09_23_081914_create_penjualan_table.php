<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('penjualan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('projek_id')->constrained('projek')->cascadeOnDelete();
            $table->date('tanggal');
            $table->string('pembeli', 150);

            // Pengepul menimbang dan membayar per kilogram, sehingga sisi
            // "keluar" dicatat dalam kilogram, bukan liter seperti Setoran.
            $table->decimal('jumlah_kg', 8, 2);
            $table->unsignedInteger('harga_per_kg');

            // Uang yang benar-benar diterima. Disimpan, bukan dihitung dari
            // jumlah × harga, karena pengepul lazim membulatkan pembayaran —
            // dan yang ditampilkan ke publik harus uang yang nyata ada.
            $table->unsignedInteger('total_rupiah');

            $table->text('catatan')->nullable();
            $table->foreignId('dicatat_oleh_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index(['projek_id', 'tanggal']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('penjualan');
    }
};
