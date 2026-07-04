<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Notice extends Model
{
    protected $fillable = ['title', 'content', 'is_pinned', 'views'];

    protected $casts = ['is_pinned' => 'boolean'];
}
