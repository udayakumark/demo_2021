<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TblPurchase extends Model
{
    use HasFactory;
    protected $table      = 'tbl_purchase';
    public $timestamps    = false;
    protected $primaryKey = 'purchase_id';

    protected $fillable = [
        'purchase_id',
        'invoice_no',
        'vendor_id',
        'inv_date',
        'user_id',
        'warehouse_id',
        'broker_id',
        'veh_no',
        'weigh1',
        'weigh2',
        'weigh3',
        'narration',
        'sub_total',
        'status',
        'delete_flag',
        'created_at',
        'updated_at'
    ];

    public function user()
    {
        return $this->belongsTo('App\Models\UserDetails', 'user_id');
    }

}
