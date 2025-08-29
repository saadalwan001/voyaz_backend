<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Attraction extends Model
{
    use HasFactory;

    protected $fillable=[
        'title',
        'description',
        'front_img',
        'back_img',
    ];

    public function tourPackages()
    {
        return $this->belongsToMany(TourPackage::class, 'attraction_tour_package');
    }

}
