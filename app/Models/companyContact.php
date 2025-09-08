<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class companyContact extends Model
{
    use HasFactory;

    protected $fillable = [
        'address',
        'phone1',
        'phone2',
        'land_p',
        'whatsapp',
        'email',
    ];
}
