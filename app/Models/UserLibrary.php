<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserLibrary extends Model
{
    use HasFactory;

    protected $table = "user_libraries";

    protected $fillable = [
        "quiz_id",
        "user_id",
        "is_completed",
        "score",
        "time_spent"
    ];
}
