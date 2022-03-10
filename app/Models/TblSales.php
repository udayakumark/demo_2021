<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TblSales extends Model
{
    use HasFactory;
    protected $table      = 'tbl_sales';
    public $timestamps    = false;
    protected $primaryKey = 'bill_id';

    protected $fillable = [
        'bill_id',
        'invoice_no',
        'dealer_id',
        'inv_date',
        'user_id',
        'sub_total',
        'status',
        'delete_flag',
        'created_at',
        'updated_at'
    ];
}
