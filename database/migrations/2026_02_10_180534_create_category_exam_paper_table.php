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
        Schema::create('category_exam_paper', function (Blueprint $table) {
            $table->id();
            // Sınav ID (Sınav silinirse bağ da silinsin -> cascade)
            $table->foreignId('exam_paper_id')->constrained('exam_papers')->onDelete('cascade');
            
            // Kategori ID (Kategori silinirse bağ da silinsin -> cascade)     
            $table->foreignId('category_id')->constrained('categories')->onDelete('cascade');
            
            // Aynı sınavı aynı kategoriye yanlışlıkla 2 kere eklemeyi engelle
            $table->unique(['exam_paper_id', 'category_id']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('category_exam_paper');
    }
};
