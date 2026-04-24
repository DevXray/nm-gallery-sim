<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('barang', function (Blueprint $table) {
            if (Schema::hasColumn('barang', 'ukuran')) {
                $table->dropColumn('ukuran');
            }
            if (Schema::hasColumn('barang', 'stok')) {
                $table->dropColumn('stok');
            }
        });
    }

    public function down()
    {
        Schema::table('barang', function (Blueprint $table) {
            $table->string('ukuran')->nullable();
            $table->integer('stok')->default(1);
        });
    }
};