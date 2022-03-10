<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CartProducts extends Model
{
    use HasFactory;

    protected $table      = 'cart_products';
    public $timestamps    = false;
    protected $primaryKey = 'id';

    protected $fillable = [
        'id',
        'product_id',
        'price_id',
        'user_id',
        'quantity',
        'date_time',
        'status'
    ];

    public function price()
    {
        return $this->belongsTo('App\Models\ProductPrices', 'price_id');
    }

    public function product()
    {
        return $this->belongsTo('App\Models\Products', 'product_id');
    }

    public function user()
    {
        return $this->belongsTo('App\Models\User', 'user_id');
    }
}
