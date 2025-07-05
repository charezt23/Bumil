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
        Schema::create('imunisasi', function (Blueprint $table) {
            $table->id();
            $table->foreignId('balita_id')->constrained('balita')->onDelete('cascade');
            $table->enum('jenis_imunisasi',[
                'DPT', 
                'Campak', 
                'Hepatitis B', 
            ]);
            $table->date('tanggal_imunisasi');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('imunisasi');
    }
};
