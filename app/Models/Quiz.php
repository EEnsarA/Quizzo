<?php

namespace App\Models;

use App\Enums\Difficulty;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Quiz extends Model
{

    use HasFactory;

    protected $fillable = [
        "user_id",
        "title",
        "subject",
        "description",
        "img_url",
        "number_of_questions",
        "number_of_options",
        "difficulty",
        "duration_minutes",
        "wrong_to_correct_ratio"
    ];

    protected static function booted()
    {
        static::creating(function ($quiz) {
            $quiz->slug = Str::slug($quiz->title);

            $originalSlug = $quiz->slug;
            $count = 2;

            while (static::where("slug", $quiz->slug)->exists()) {  //Aynı slug varsa sonuna 2 den başlayark ekleme
                $quiz->slug = $originalSlug . "-" . $count++;
            }
        });
    }

    public function rankings($filters = [])
    {
        $query = QuizResult::whereNotIn("time_spent", [0])->where("quiz_id", $this->id)->with("user");

        if (in_array("multiple_attempts", $filters)) {
            $query->orderByDesc("net")->orderBy("time_spent");
        } elseif (in_array("best_time", $filters)) {
            $query->where("attempt_number", 1)->orderBy("time_spent")->orderByDesc("net");
        } elseif (in_array("multiple_attempts", $filters) && in_array("best_time", $filters)) {
            $query->orderBy("time_spent")->orderByDesc("net");
        } else {
            $query->where("attempt_number", 1)->orderByDesc("net")->orderBy("time_spent");
        }

        return $query->get();
    }



    public function getRouteKeyName()
    {
        return "slug";
    }

    public function questions()
    {
        return $this->hasMany(Question::class); // fkey hangi tablodaysa o tablodan diğerine belongsTo 
    }

    public function user()
    {
        return $this->belongsTo(User::class); // fkey hangi tablodaysa o tablodan diğerine belongsTo  fkey quiz tablosunda olduğundan quizden usera belongsto
    }

    public function solvers()
    {
        return $this->belongsToMany(User::class, "user_libraries")
            ->withPivot("is_completed", "score", "time_spent", "id")
            ->withTimestamps();
    }

    public function results()
    {
        return $this->hasMany(QuizResult::class)->whereNotIn("time_spent", [0]);
    }


    protected $casts = [
        "difficulty" => Difficulty::class,
    ];
}
