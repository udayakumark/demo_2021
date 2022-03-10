<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TblProduction extends Model
{
    use HasFactory;
    protected $table      = 'tbl_production';
    public $timestamps    = false;
    protected $primaryKey = 'id';

    protected $fillable = [
        'id',
        'source_qty',
        'source_total',
        'destination_total',
        'narration',
        'status',
        'created_at',
        'updated_at'
    ];

}
