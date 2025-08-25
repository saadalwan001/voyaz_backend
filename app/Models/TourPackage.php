<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TourPackage extends Model
{
    use HasFactory;

    protected $fillable=[
        'title',
        'total_days',
        'description',
        'main_image',
        'sub_image1',
        'sub_image2',
        'sub_image3',
        'sub_image4',
        'enabled',

    ];

    protected $casts=[
        'enabled' => 'boolean',
        'total_days' => 'integer'
    ];

    public function itineraries(){
        return $this->hasMany(Itinerary::class);
    }
}


