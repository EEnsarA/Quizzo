<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Question extends Model
{
    use HasFactory;

    protected $fillable = [
        "quiz_id",
        "title",
        "question_text",
        "img_url",
        "points"
    ];

    public function quiz()
    {
        return $this->belongsTo(Quiz::class); // quiz fkey i question tablosunda olduğundan Question->belongsTo(Quiz);
    }

    public function answers()
    {
        return $this->hasMany(Answer::class);
    }
}
