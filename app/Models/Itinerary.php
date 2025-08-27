<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Itinerary extends Model
{
    use HasFactory;


    protected $fillable = [
        'tour_package_id' ,
        'day_title',
        'description',
        'include_toggle',

    ];

    protected $casts =[
        'include_toggle' => 'boolean',


    ];

    public function tourPackage(){
        return $this->belongsTo(TourPackage::class);
    }
}
