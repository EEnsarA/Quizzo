<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserDocument extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'file_name',
        'file_path',
        'file_size',
        'file_type',
    ];

    // Bu doküman hangi kullanıcıya ait?
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
