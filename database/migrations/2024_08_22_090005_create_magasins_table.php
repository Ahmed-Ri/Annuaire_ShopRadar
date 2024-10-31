<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMagasinsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('magasins', function (Blueprint $table) {
            // Clé primaire en chaîne de caractères
            $table->string('id')->primary();

            // Autres colonnes correspondant au modèle
            $table->string('nom');
            $table->string('ACTIVITE')->nullable();
            $table->string('adresse');
            $table->string('tel');
            $table->string('IDE');
            $table->string('AdresseCourte')->nullable();
            $table->string('CP')->nullable(); // Code postal
            $table->string('VILLE')->nullable();           
            $table->string('TEFET')->nullable();
            $table->string('LIBTEFET')->nullable();
            $table->string('SIRET')->nullable();
            $table->string('MOBILE')->nullable();
            $table->string('EMAIL')->nullable();
            $table->string('image')->nullable();

            // Timestamps
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('magasins');
    }
}
