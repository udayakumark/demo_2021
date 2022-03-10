<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UniqueNo extends Model
{
    use HasFactory;
    protected $table      = 'unique_no';
    public $timestamps    = false;
    protected $primaryKey = 'id';

    protected $fillable = [
        'name',
        'number',
        'status'
    ];

}
