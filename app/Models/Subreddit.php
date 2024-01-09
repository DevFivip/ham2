<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Subreddit extends Model
{
    use HasFactory;

    protected $fillable = [
        "name",
        "tags",
        "verification",
        "status"
    ];

    protected $casts = [
        'tags' => 'array',
        'verification' => 'boolean',
        'status' => 'boolean',
    ];

    public function onlyfans()
    {
        return $this->belongsToMany(Onlyfan::class, 'onlyfans_subreddits', 'subreddit_id');
    }
}
