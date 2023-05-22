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
        Schema::create('products', function (Blueprint $table) {
            $table->id();

            /* Add additional fields */
            $table->text('title');  // product title
            $table->text('description')->default("");   // description
            $table->text('short_notes');   // short notes
            $table->decimal('price', 10, 2); // price
            $table->text('image')->nullable(); // product image
            $table->text('slug')->default(""); // product slug
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
