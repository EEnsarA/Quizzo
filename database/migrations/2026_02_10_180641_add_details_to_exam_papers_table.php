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
        Schema::table('exam_papers', function (Blueprint $table) {
            // is_public: Varsayılan kapalı (false)
            $table->boolean('is_public')->default(false)->after('page_count'); 
            
            // description: İsteğe bağlı açıklama alanı
            $table->text('description')->nullable()->after('is_public');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('exam_papers', function (Blueprint $table) {
            $table->dropColumn(['is_public', 'description']);
        });
    }
};
