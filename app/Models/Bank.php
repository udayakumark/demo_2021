<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Bank extends Model
{
    use HasFactory;
    protected $table      = 'bank';
    public $timestamps    = false;
    protected $primaryKey = 'id';

    protected $fillable = [
        'id',
        'bank_name',
        'account_name',
        'account_no',
        'ifsc',
        'mobile_no',
        'branch',
        'current_balance',
        'bank_address',
        'type',
        'date_time',
        'status'
    ];
}
