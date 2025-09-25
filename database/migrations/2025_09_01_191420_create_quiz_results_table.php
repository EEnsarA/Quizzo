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
        Schema::create('quiz_results', function (Blueprint $table) {
            $table->id();
            $table->foreignId("quiz_id")->constrained()->onDelete("cascade");
            $table->foreignId("user_id")->nullable()->constrained()->onDelete("cascade");
            $table->string('session_id')->nullable();
            $table->json('details')->nullable();
            $table->integer("correct_count")->default(0);
            $table->integer("wrong_count")->default(0);
            $table->integer("empty_count")->default(0);
            $table->float("net")->default(0);
            $table->integer("time_spent")->default(0);
            $table->integer("attempt_number")->default(1);
            $table->timestamps();
            $table->timestamp('expires_at')->nullable();
            $table->timestamp("started_at")->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('quiz_results');
    }
};
