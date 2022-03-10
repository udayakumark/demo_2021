<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SupplierPincodes extends Model
{
    use HasFactory;
    protected $table      = 'supplier_pincodes';
    public $timestamps    = false;
    protected $primaryKey = 'id';

    protected $fillable = [
        'id',
        'user_id',
        'pincode_id',
        'date_time',
        'status'
    ];

    public function user()
    {
        return $this->belongsTo('App\Models\Users', 'user_id');
    }

    public function pincode()
    {
        return $this->belongsTo('App\Models\Pincodes', 'pincode_id');
    }
}
