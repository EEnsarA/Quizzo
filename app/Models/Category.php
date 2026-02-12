<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $fillable = ['name', 'slug', 'icon'];

    // İLİŞKİ: Bu kategoride birçok sınav olabilir
    public function exam_papers()
    {
        return $this->belongsToMany(ExamPaper::class, 'category_exam_paper');
    }
}
