<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PurchasesItem extends Model
{
    use HasFactory;
    protected $table      = 'purchases_items';
    public $timestamps    = false;
    protected $primaryKey = 'id';

    protected $fillable = [
        'id',
        'purchase_id',
        'paddy_id',
        'nos',
        'qty',
        'rate',
        'per',
        'total',
        'deleteflag'
    ];

    public function sales()
    {
        return $this->belongsTo('App\Models\TblPurchases', 'purchase_id');
    }

    public function get_products()
    {
        return $this->belongsTo('App\Models\Paddy', 'id');
    }
}
