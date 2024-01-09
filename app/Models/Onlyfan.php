<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Onlyfan extends Model
{
    use HasFactory;
    protected $fillable = ['name', 'username', 'cant_suscriptores', 'cant_fotos', 'cant_videos', 'cant_posts', 'precio_membresia', 'show_more_social_medias', 'usuarios_comunicacion', 'cant_ganancias', 'tiempo_creacion', 'description', 'user_id', 'slug', 'url', 'imagen', 'banner', 'views', 'status'];

    public function user()
    {
        $this->belongsTo(User::class);
    }

    public function subreddits()
    {
        return $this->belongsToMany(Subreddit::class,'onlyfans_subreddits','model_id');
    }
}
