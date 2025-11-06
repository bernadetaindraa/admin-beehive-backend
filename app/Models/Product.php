<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'subtitle',
        'description',
        'type',
        'wingspan',
        'flight_endurance',
        'flight_range',
        'flight_height',
        'other_details',
        'base_price',
        'images',
        'include_items',
        'package_options',
        'financing',
    ];

    protected $casts = [
        'images' => 'array',
        'include_items' => 'array',
        'package_options' => 'array',
        'financing' => 'array',
    ];
}