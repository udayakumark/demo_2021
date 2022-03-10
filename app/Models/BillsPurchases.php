<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BillsPurchases extends Model
{
    use HasFactory;
    protected $table      = 'bill_purchases';
    public $timestamps    = false;
    protected $primaryKey = 'id';

    protected $fillable = [
        'id',
        'bill_id',
        'wh_id',
        'product_id',
        'qty',
        'price',
        'final_price',
        'total'
    ];
}
