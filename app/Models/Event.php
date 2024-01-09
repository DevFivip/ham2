<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    use HasFactory;

    protected $fillable = [
        "user_id",
        "model_id",
        "subreddit_id",
        "status",
        "posted_at"
    ];


    public function user()
    {
        return $this->hasOne(User::class);
    }
    public function model()
    {
        return $this->hasOne(Onlyfan::class, 'model_id');
    }
    public function subreddit()
    {
        return $this->hasOne(Subreddit::class);
    }
}
