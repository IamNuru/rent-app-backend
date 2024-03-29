<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Request extends Model
{
    use HasFactory;
    protected $guarded = [];
    protected $casts = [
        'addresses' => 'array',
        'amenities' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
