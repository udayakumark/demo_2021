<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Rice extends Model
{
    use HasFactory;
    protected $table      = 'rice_types';
    public $timestamps    = false;
    protected $primaryKey = 'id';

    protected $fillable = [
        'id',
        'original_name',
        'bag_type',
        'duplicate_name',
        'dealers_price',
        'customers_price',
        'onlinesales_price'
    ];

}
