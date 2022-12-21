<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PropertyImages extends Model
{
    use HasFactory;

    protected $table = "properties_images";

    public function properties(){
        return $this->belongsToMany(Property::class);
    }
}
