<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('profil_toko', function (Blueprint $table) {
            $table->id();
            $table->string('nama_toko')->default('NM Gallery');
            $table->string('pemilik')->default('Nurhayati');
            $table->string('telepon')->default('+62 411-xxx-xxxx');
            $table->text('alamat')->default('Jl. Somba Opu No. 12, Makassar');
            $table->string('instagram')->default('@nmgallery.id');
            $table->string('logo')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('profil_toko');
    }
};