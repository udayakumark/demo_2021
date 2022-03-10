<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TblBill extends Model
{
    use HasFactory;
    protected $table      = 'tbl_billing';
    public $timestamps    = false;
    protected $primaryKey = 'bill_id';

    protected $fillable = [
        'bill_id',
        'invoice_no',
        'vendor_id',
        'inv_date',
        'user_id',
        'warehouse_id',
        'veh_no',
        'hsn',
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
