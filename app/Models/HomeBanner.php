<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HomeBanner extends Model
{
    use HasFactory;
    protected $table      = 'home_banners';
    public $timestamps    = false;
    protected $primaryKey = 'id';

    protected $fillable = [
        'id',
        'product_id',
        'image',
        'title',
        'sub_title',
        'description',
        'date_time',
        'status'
    ];

    public function product()
    {
        return $this->belongsTo('App\Models\Products', 'product_id');
    }
}
