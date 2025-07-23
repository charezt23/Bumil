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
        Schema::create('balita', function (Blueprint $table) {
            $table->id();
            $table->string('nama');
            $table->string('nik')->unique();
            $table->string('nama_ibu');
            $table->date('tanggal_lahir');
            $table->text('alamat');
            $table->enum('jenis_kelamin', ['L', 'P']);
            $table->foreignId('posyandu_id')->constrained('posyandu')->onDelete('restrict');
            $table->enum('Buku_KIA', ['ada', 'tidak_ada']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('balita');
    }
};
