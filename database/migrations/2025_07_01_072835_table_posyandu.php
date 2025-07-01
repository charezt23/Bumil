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
        Schema::create('posyandu', function (Blueprint $table) {
            $table->id();
            //value kolom ingin buat kolom user_id yang id nya diambil dari tabel users, buatkan migrasinya
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('nama_posyandu');
            $table->timestamps(); // Menambahkan created_at dan updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('posyandu');
    }
};
