<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Post extends Model
{

    use HasFactory;

    /*protected $casts = [
        'created_at' => 'datetime: Y-m-d H:00'
    ];*/
    /* protected $casts = [
        'birthday'  => 'date:Y-m-d',
        'joined_at' => 'datetime: Y-m-d H:00',
    ]; */
    protected $guarded = [];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
