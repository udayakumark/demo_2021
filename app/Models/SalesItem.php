<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SalesItem extends Model
{
    use HasFactory;
    protected $table      = 'sales_items';
    public $timestamps    = false;
    protected $primaryKey = 'id';

    protected $fillable = [
        'id',
        'bill_id',
        'product_id',
        'Qtl',
        'qty',
        'price',
        'total',
        'deleteflag'
    ];

    public function sales()
    {
        return $this->belongsTo('App\Models\TblSales', 'bill_id');
    }

    public function get_products()
    {
        return $this->belongsTo('App\Models\Products', 'product_id');
    }
}
