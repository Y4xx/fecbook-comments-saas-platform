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
        Schema::create('facebook_comments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('facebook_page_id')->constrained()->onDelete('cascade');
            $table->string('facebook_comment_id')->unique();
            $table->string('post_id');
            $table->text('message');
            $table->string('author_name');
            $table->string('author_id');
            $table->timestamp('comment_created_time');
            $table->enum('sentiment_status', ['pending', 'analyzing', 'analyzed', 'failed'])->default('pending');
            $table->timestamps();
            
            $table->index(['facebook_page_id', 'sentiment_status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('facebook_comments');
    }
};
