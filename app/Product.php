<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $table = 'producto';

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
        return $this->belongsTo(Category::class, "categorias_id");
    }

    public function subcategoria()
    {
        return $this->belongsTo(SubCategoria::class, "subcategorias_id");
    }

    public function sub_subcategoria()
    {
        return $this->belongsTo(SubSubCategoria::class, "sub_subcategorias_id");
    }

    public function images()
    {
        return $this->hasMany(Image::class);
    }
}
