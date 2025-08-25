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
        'included_items',
        'excluded_items',
    ];

    protected $casts =[
        'include_toggle' => 'boolean',
        'included_items' => 'array',
        'excluded_items' => 'array',

    ];

    public function tourPackage(){
        return $this->belongsTo(TourPackage::class);
    }
}
