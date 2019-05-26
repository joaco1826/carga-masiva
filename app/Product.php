<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = [
        "name",
        "reference",
        "price",
        "discount",
        "image",
        "category_id",
        "brand_id",
        "description",
        "featured",
        "stock",
        "status"
    ];

    public function labels()
    {
        return $this->belongsToMany(
            Label::class, "products_labels", "products_id", "labels_id"
        );
    }

    public function sizes()
    {
        return $this->belongsToMany(
            Size::class, "products_sizes", "products_id", "sizes_id"
        );
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function brand()
    {
        return $this->belongsTo(Brand::class);
    }
}
