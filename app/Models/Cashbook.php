<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cashbook extends Model
{
    use HasFactory;
    protected $table      = 'cashbook';
    public $timestamps    = false;
    protected $primaryKey = 'id';

    protected $fillable = [
        'id',
        'user_id',
        'type',
        'amount',
        'status'
    ];

    public function user()
    {
        return $this->belongsTo('App\Models\UserDetails', 'user_id');
    }

}
