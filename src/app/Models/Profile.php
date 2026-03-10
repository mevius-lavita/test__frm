<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Profile extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id',
        'profile_img',
        'address_number',
        'address',
        'building',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    public function likes()
    {
        return $this->hasMany(ItemLike::class);
    }

    public function purchasedItems()
    {
        return $this->hasMany(PurchasedItem::class);
    }

    public function listedItems()
    {
        return $this->hasMany(ListedItem::class);
    }
}
