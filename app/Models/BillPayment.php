<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BillPayment extends Model
{
    use HasFactory;
    protected $table      = 'bill_payment';
    public $timestamps    = false;
    protected $primaryKey = 'id';

    protected $fillable = [
        'id',
        'bill_id',
        'bank_id',
        'online_pay_id',
        'cash_amount',
        'credit_amount',
        'created_at'
    ];
}
