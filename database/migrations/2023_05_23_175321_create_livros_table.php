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
        Schema::create('livros', function (Blueprint $table) {
            $table->id();

            /* Add additional fields */
            //id, usuario_publicador_id , titulo
            $table->text('titulo');
            $table->unsignedBigInteger('usuario_publicador_id');
            //caso o usuario seja deletado, cascateia o delete para os índices do livro deletado
            $table->foreign('usuario_publicador_id')
                ->references('id')->on('users')->onDelete('cascade');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('livros');
    }
};
