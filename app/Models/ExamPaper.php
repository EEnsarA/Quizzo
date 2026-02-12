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
        'is_public' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    // İLİŞKİ: Bu sınavın birçok kategorisi olabilir
    public function categories()
    {
        return $this->belongsToMany(Category::class, 'category_exam_paper');
    }
}
