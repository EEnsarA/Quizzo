<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExamPaper extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'canvas_data' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
