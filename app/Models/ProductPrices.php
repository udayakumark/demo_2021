<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductPrices extends Model
{
    use HasFactory;

    protected $table      = 'product_prices';
    public $timestamps    = false;
    protected $primaryKey = 'id';

    protected $fillable = [
        'id',
        'category_id',
        'product_id',
        'price_type',
        'quantity',
        'original_price',
        'selling_price',
        'discount_price',
        'discount_percentage',
        'date_time',
        'status'
    ];

    public function category()
    {
        return $this->belongsTo('App\Models\ProductCategories', 'category_id');
    }

    public function product()
    {
        return $this->belongsTo('App\Models\Products', 'product_id');
    }
}
