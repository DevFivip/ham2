<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Subreddit extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'tags', 'verification', 'category', 'status', 'description', 'days_for_middle'];

    protected $casts = [
        'tags' => 'array',
        'verification' => 'boolean',
        'status' => 'boolean',
    ];

    public function onlyfans()
    {
        return $this->belongsToMany(Onlyfan::class, 'onlyfans_subreddits', 'subreddit_id');
    }

    public function getFullDescriptionAttribute()
    {
        return $this->name . ' (' . implode(', ', $this->tags) . ') ' . $this->description;
    }
    static function tags()
    {
        $tag = Subreddit::get()
            ->flatMap(function ($subreddit) {
                $arr = [];
                $tags = $subreddit->tags;
                // dd($tags);
                $tags = array_map('trim', $tags);
                $_tags = [];
                foreach ($tags as $key => $value) {
                    $_tags[$value] = $value;
                }
                return $_tags;
            })
            ->unique();

        return $tag;
    }
}
