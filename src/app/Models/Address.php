<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Address extends Model
{
    use HasFactory;
    protected $fillable = [
        'address_number',
        'postal_code',
        'address',
        'building',
    ];
    public function profile()
    {
        return $this->hasOne(Profile::class);
    }
}
