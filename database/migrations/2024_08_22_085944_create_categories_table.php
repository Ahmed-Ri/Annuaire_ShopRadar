<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCategoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('categories', function (Blueprint $table) {
            $table->string('id')->primary(); // Utiliser une chaîne de caractères comme clé primaire
            $table->string('name');
            $table->string('image')->nullable();
            $table->string('parent_id')->nullable(); // `parent_id` sera également une chaîne
            $table->timestamps();
    
            // Clé étrangère pour `parent_id` reliant une catégorie à une autre
            $table->foreign('parent_id')->references('id')->on('categories')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('categories');
    }
}
