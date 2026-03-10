<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    use HasFactory;

    protected $fillable = [
        'comment_detail',
        'profile_id',
        'item_id',
    ];

    public function item()
    {
        return $this->belongsTo(Item::class);
    }

    public function profile()
    {
        return $this->belongsTo(Profile::class);
    }
}
