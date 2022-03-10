<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PettyCashModel extends Model
{
    use HasFactory;
    protected $table      = 'petty_cash';
    public $timestamps    = false;
    protected $primaryKey = 'id';

    protected $fillable = [
        'id',
        'amount',
        'comments',
        'date_time',
        'status'
    ];
}
