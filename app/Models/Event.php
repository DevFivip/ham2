<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use Illuminate\Support\Str;

class Event extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'model_id', 'subreddit_id', 'status', 'posted_at'];

    public function user()
    {
        return $this->hasOne(User::class);
    }
    public function onlyfan()
    {
        return $this->hasOne(Onlyfan::class, 'id', 'model_id');
    }
    public function subreddit()
    {
        return $this->hasOne(Subreddit::class, 'id', 'subreddit_id');
    }

    public function getFullDescriptionAttribute()
    {
        // return $this->model_id . ' (' . implode(', ', $this->tags) . ")";

        switch ($this->status) {
            case 1:
                $icon = '❌';
                break;
            case 2:
                $icon = '🕑';
                break;
            case 3:
                $icon = '✅';
                break;

            default:
                $icon = '🤷‍♂️';
                break;
        }
        return $icon . ' ' . Str::words($this->onlyfan->name, 1, '') . ' ' . $this->subreddit->name;
    }
}
