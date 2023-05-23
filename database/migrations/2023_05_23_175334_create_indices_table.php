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
        Schema::create('indices', function (Blueprint $table) {
            $table->id();

            /* Add additional fields */
            //id, livro_id, indice_pai_id, titulo, pagina
            $table->unsignedBigInteger('livro_id');
            $table->unsignedBigInteger('indice_pai_id')->nullable();
            $table->text('titulo');
            $table->unsignedSmallInteger('pagina');
            //caso o livro seja deletado, cascateia o delete para os índices do livro deletado
            $table->foreign('livro_id')
                ->references('id')->on('livros')->onDelete('cascade');
            $table->foreign('indice_pai_id')
                ->references('id')->on('indices')->onDelete('cascade');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('indices');
    }
};
