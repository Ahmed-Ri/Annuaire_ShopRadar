<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMagasinCategoryTable extends Migration
{
    public function up()
    {
        Schema::create('magasin_category', function (Blueprint $table) {
            $table->id();
            $table->string('magasin_id'); // chaîne pour `magasin_id`
            $table->string('category_id'); // chaîne pour `category_id`
            $table->string('subcategory_id')->nullable(); // sous-catégorie également en chaîne
            $table->string('main_category_id')->nullable(); // catégorie principale en chaîne
            $table->string('type')->default('category');
            $table->timestamps();
    
            // Définir les relations avec les clés étrangères
            $table->foreign('magasin_id')->references('id')->on('magasins')->onDelete('cascade');
            $table->foreign('category_id')->references('id')->on('categories')->onDelete('cascade');
            $table->foreign('subcategory_id')->references('id')->on('categories')->onDelete('cascade');
            $table->foreign('main_category_id')->references('id')->on('categories')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('magasin_category');
    }
}
