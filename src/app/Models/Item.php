<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
    use HasFactory;
    protected $fillable = [
        'item_img',
        'item_name',
        'brand_name',
        'item_detail',
        'price',
        'category_id',
        'condition',
    ];
    public function category()
    {
        return $this->belongsTo(Category::class);
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
    public function categories()
    {
        return $this->belongsToMany(Category::class, 'item_category');
    }
    public function scopeKeywordSearch($query, $keyword)
    {
        if (!empty($keyword)) {
            $query->where('item_name', 'like', '%' . $keyword . '%');
        }
        return $query;
    }
}
