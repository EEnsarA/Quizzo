<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class QuizResult extends Model
{
    protected $fillable = [
        "quiz_id",
        "user_id",
        "session_id",
        "details",
        "correct_count",
        "wrong_count",
        "empty_count",
        "net",
        "time_spent",
        "attempt_number",
        'started_at',
    ];

    protected $casts = [
        "details" => "array", // Laravel otomatik JSON encode/decode yapıyor
        "started_at" => "datetime" // string olarak çekildiği için tekrar datetime dönüştürme
    ];

    public function quiz()
    {

        return $this->belongsTo(Quiz::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
