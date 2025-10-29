<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('comments', function (Blueprint $table) {
            $table->id();
            
            $table->morphs('commentable');
            $table->morphs('commenter');
            
            $table->text('comment');
            $table->timestamp('approved_at')->nullable();
            $table->json('extra_attributes')->nullable();
            
            $table->foreignId('parent_id')->nullable()->constrained('comments')->cascadeOnDelete();
            
            $table->timestamps();
        });
    }
    
    public function down(): void
    {
        Schema::dropIfExists('comments');
    }
};