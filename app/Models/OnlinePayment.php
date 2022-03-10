<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OnlinePayment extends Model
{
    use HasFactory;
    protected $table      = 'bank_mode_of_payment';
    public $timestamps    = false;
    protected $primaryKey = 'id';

    protected $fillable = [
        'id',
        'payment_type',
        'mobile_no',
        'bank_id',
        'date_time',
        'status'
    ];
}
