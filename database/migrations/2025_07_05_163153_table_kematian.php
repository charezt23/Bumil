<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('kematian', function (Blueprint $table) {
            $table->id();
            $table->foreignId('balita_id')->unique()->constrained('balita')->onDelete('cascade');
            $table->date('tanggal_kematian');
            $table->text('penyebab_kematian');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kematian');
    }
};
