<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RiceBenefits extends Model
{
    use HasFactory;
    protected $table      = 'rice_benefits';
    public $timestamps    = false;
    protected $primaryKey = 'id';

    protected $fillable = [
        'id',
        'title',
        'image',
        'description',
        'date_time',
        'status'
    ];
}
