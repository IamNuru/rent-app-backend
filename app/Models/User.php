<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $guarded = [];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];


    public function posts()
    {
        return $this->hasMany(Post::class);
    }

    public function tenants()
    {
        return $this->hasMany(Tenant::class);
    }


    public function requests()
    {
        return $this->hasMany(Request::class);
    }

    public function properties()
    {
        return $this->hasMany(Property::class);
    }
    

    public function messages()
    {
        return $this->hasMany(Message::class, 'sender_id', 'id');
    }

    public function message()
    {
        return $this->belongsToMany(Message::class, 'messages', 'sender_id', 'sender_id', 'id');
    }

    public function notifications()
    {
        return $this->hasMany(Notification::class, 'receiver_id', 'id');
    }
    public function unReadNotifications()
    {
        return $this->hasMany(Notification::class, 'receiver_id', 'id')->where('read', false);
    }
    public function ReadNotifications()
    {
        return $this->hasMany(Notification::class, 'receiver_id', 'id')->where('read', true);
    }
}
