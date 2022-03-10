<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderedProducts extends Model
{
    use HasFactory;
    protected $table      = 'ordered_products';
    public $timestamps    = false;
    protected $primaryKey = 'id';

    protected $fillable = [
        'id',
        'productprice_id',
        'order_id',
        'product_name',
        'quantity',
        'price',
        'total_price',
        'date_time',
        'status'
    ];

    public function productPrice()
    {
        return $this->belongsTo('App\Models\ProductPrices', 'productprice_id');
    }

    public function order()
    {
        return $this->belongsTo('App\Models\Orders', 'order_id');
    }
}
