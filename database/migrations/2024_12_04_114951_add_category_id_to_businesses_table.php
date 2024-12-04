<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCategoryIdToBusinessesTable extends Migration
{
    public function up()
    {
        Schema::table('businesses', function (Blueprint $table) {
            $table->unsignedBigInteger('category_id')->nullable(); // Agrega la columna category_id
            $table->foreign('category_id')->references('id')->on('categories')->onDelete('set null'); // Define la relación con la tabla categories
        });
    }

    public function down()
    {
        Schema::table('businesses', function (Blueprint $table) {
            $table->dropForeign(['category_id']); // Eliminar la clave foránea
            $table->dropColumn('category_id'); // Eliminar la columna category_id
        });
    }
}
