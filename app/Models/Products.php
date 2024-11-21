<?php

namespace App\Models;

class Products extends BaseModel
{
    protected string $collection = 'products';

    protected $fillable = [
        'product',
        'description',
        'price',
        'from'
    ];
}
