<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class States extends Model
{
    use HasFactory;
    protected $table      = 'states';
    public $timestamps    = false;
    protected $primaryKey = 'id';

    protected $fillable = [
        'id',
        'name',
        'country_id',
        'flag'
    ];

    public function country()
    {
        return $this->belongsTo('App\Models\Countries', 'country_id');
    }
}
