<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Property extends Model
{
    use HasFactory;
    protected $guarded = [];

    protected $casts = [
        'amenities' => 'array',
        'addresses' => 'array',
    ];

    public function amenities()
    {
        return $this->hasMany(Amenities::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function imageslist(){
        return $this->hasMany(PropertyImages::class);
    }
    
    
    public function images(){
        return $this->belongsToMany(PropertyImages::class, 'properties_images', 'property_id', 'property_id', 'id');
    }
}
