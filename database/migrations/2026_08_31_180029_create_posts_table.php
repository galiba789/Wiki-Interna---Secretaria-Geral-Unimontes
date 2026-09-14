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
        Schema::create('posts', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->longText('content'); // longText pois o tutorial pode ter imagens e textos longos

        // Relacionamentos (Chaves Estrangeiras)
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
        // Se deletar a categoria, não deleta o post, apenas deixa nulo (opcional, mas seguro)
            $table->foreignId('category_id')->nullable()->constrained()->onDelete('set null');
            $table->timestamps();

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('posts');
    }
};
