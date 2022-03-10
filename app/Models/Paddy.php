<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Paddy extends Model
{
    use HasFactory;
    protected $table      = 'paddy_products';
    public $timestamps    = false;
    protected $primaryKey = 'id';

    protected $fillable = [
        'id',
        'name',
        'type'
    ];

}
