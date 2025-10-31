<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'location',
        'goal',
        'product_service_id',
        'industry_id',
        'image',
    ];

    public function productService()
    {
        return $this->belongsTo(ProductService::class);
    }

    public function industry()
    {
        return $this->belongsTo(Industry::class);
    }
}
