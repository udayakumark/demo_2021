<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pincodes extends Model
{
    use HasFactory;
    protected $table      = 'pincodes';
    public $timestamps    = false;
    protected $primaryKey = 'id';

    protected $fillable = [
        'id',
        'name',
        'city_id',
        'state_id',
        'country_id',
        'date_time',
        'status'
    ];

    public function country()
    {
        return $this->belongsTo('App\Models\Countries', 'country_id');
    }

    public function state()
    {
        return $this->belongsTo('App\Models\States', 'state_id');
    }

    public function city()
    {
        return $this->belongsTo('App\Models\Cities', 'city_id');
    }
}
