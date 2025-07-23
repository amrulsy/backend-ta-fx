<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{

    use HasFactory;



    protected $fillable = [
        'name',
        'description',
        'price',
        'stock',
        'category',
        'category_id',
        'image',
        'is_best_seller'
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function getImageUrlAttribute()
    {
        if (!$this->image) {
            return null;
        }
        
        // Use the configured APP_URL from .env instead of url() helper
        $baseUrl = config('app.url', 'http://localhost:8000');
        return $baseUrl . '/storage/products/' . $this->image;
    }

    protected $appends = ['image_url'];
}
