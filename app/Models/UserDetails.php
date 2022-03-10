<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserDetails extends Model
{
    use HasFactory;

    protected $table      = 'user_details';
    public $timestamps    = false;
    protected $primaryKey = 'id';

    protected $fillable = [
        'id',
        'user_id',
        'first_name',
        'last_name',
        'dob',
        'country_id',
        'state_id',
        'city_id',
        'pincode',
        'address',
        'aadhar_image',
        'aadhar',
        'type',
        'date_time',
        'status'
    ];

    public function user()
    {
        return $this->belongsTo('App\Models\User', 'user_id');
    }

    public function cashbook()
    {
        return $this->hasMany('App\Models\Cashbook', 'user_id');
    }

    public function purchase()
    {
        return $this->hasMany('App\Models\TblPurchase', 'user_id');
    }
}
