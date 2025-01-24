<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Place extends Model
{
    protected $fillable = [
        'title',
        'description',
        'image',
        'lat',
        'lng',
    ];
}
