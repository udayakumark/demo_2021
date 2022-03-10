<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BillsItem extends Model
{
    use HasFactory;
    protected $table      = 'bill_items';
    public $timestamps    = false;
    protected $primaryKey = 'id';

    protected $fillable = [
        'id',
        'purchase_id',
        'paddy_id',
        'wgt',
        'qty',
        'price',
        'final_price',
        'total',
        'deleteflag'
    ];

    public function sales()
    {
        return $this->belongsTo('App\Models\TblBill', 'bill_id');
    }

    public function get_products()
    {
        return $this->belongsTo('App\Models\Paddy', 'id');
    }
}
