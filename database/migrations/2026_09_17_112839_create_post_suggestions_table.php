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
        Schema::create('post_suggestions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('post_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('parent_id')->nullable()->constrained('post_suggestions')->cascadeOnDelete();
            $table->text('content');
            $table->string('status')->default('open');
            $table->boolean('is_pinned')->default(false);
            $table->timestamps();

            $table->index(['post_id', 'parent_id']);
            $table->index(['post_id', 'is_pinned']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('post_suggestions');
    }
};
