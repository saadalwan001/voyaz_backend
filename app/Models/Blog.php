<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Blog extends Model
{
    protected $fillable= [
        'admin_id',
        'title',
        'description',
        'publisher_date',
        'img_url',
        'category'


        ];

    public function admin()
    {
        return $this->belongsTo(Admin::class);
    }

    public function comments()
    {
        return $this->hasMany(Blog::class);
    }
}
