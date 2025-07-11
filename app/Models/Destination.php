<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Destination extends Model
{
    protected $table= 'destinations';
    protected $primaryKey = 'id';
    protected $keyType = "int";
    public $timestamps = true;
    public $incrementing = true;

    protected $fillable = [
        'name',
        'location',
        'latitude',
        'longitude',
        'category',
        'average_rating',
        'image_url',
        'approx_price_range',
        'best_time_to_visit'
    ];
}
