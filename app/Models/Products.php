<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Products extends Model
{
    use HasFactory;

    protected $table      = 'products';
    public $timestamps    = false;
    protected $primaryKey = 'id';

    protected $fillable = [
        'id',
        'category_id',
        'product_name',
        'product_code',
        'product_description',
        'product_image',
        'date_time',
        'status'
    ];

    public function category()
    {
        return $this->belongsTo('App\Models\ProductCategories', 'category_id');
    }
}
