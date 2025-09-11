<?php

use App\Enums\Difficulty;
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
        Schema::create('quizzes', function (Blueprint $table) {
            $table->id();
            $table->foreignId("user_id")->constrained()->onDelete("cascade");
            $table->string("title");
            $table->string("subject");
            $table->text("description")->nullable();
            $table->string("img_url")->nullable();
            $table->integer("number_of_questions")->default(5);
            $table->integer("number_of_options")->default(5);
            $table->enum("difficulty", Difficulty::cases())->default(Difficulty::Easy);
            $table->integer("duration_minutes")->default(5);
            $table->integer("wrong_to_correct_ratio")->default(4);
            $table->string("slug")->unique();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('quizzes');
    }
};
