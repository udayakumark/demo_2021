<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cities extends Model
{
    use HasFactory;
    protected $table      = 'cities';
    public $timestamps    = false;
    protected $primaryKey = 'id';

    protected $fillable = [
        'id',
        'name',
        'state_id',
        'country_id',
        'flag'
    ];

    public function country()
    {
        return $this->belongsTo('App\Models\Countries', 'country_id');
    }

    public function state()
    {
        return $this->belongsTo('App\Models\States', 'state_id');
    }
}
