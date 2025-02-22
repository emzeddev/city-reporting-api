<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Report extends Model
{
    protected $fillable = [
        'title',
        'description',
        'latitude',
        'longitude',
        'image',
        'status'
    ];
}
