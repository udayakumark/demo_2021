<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DestinationItems extends Model
{
    use HasFactory;
    protected $table      = 'destination_items';
    public $timestamps    = false;
    protected $primaryKey = 'id';

    protected $fillable = [
        'id',
        'item_id',
        'warehouse_id',
        'qty',
        'rate',
        'amount',
        'production_id'
    ];

    public function get_products()
    {
        return $this->belongsTo('App\Models\Paddy', 'item_id');
    }

    public function get_warehouse()
    {
        return $this->belongsTo('App\Models\Warehouses', 'warehouse_id');
    }

    public function get_production()
    {
        return $this->belongsTo('App\Models\TblProduction', 'production_id');
    }
}
