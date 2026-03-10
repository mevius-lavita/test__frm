<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PurchasedItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'profile_id',
        'item_id',
        'payment_method',
    ];

    public function profile()
    {
        return $this->belongsTo(Profile::class);
    }

    public function item()
    {
        return $this->belongsTo(Item::class);
    }
}
