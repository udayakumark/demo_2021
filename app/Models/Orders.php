<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Orders extends Model
{
    use HasFactory;
    protected $table      = 'orders';
    public $timestamps    = false;
    protected $primaryKey = 'id';

    protected $fillable = [
        'id',
        'user_id',
        'supplier_id',
        'payment_type',
        'payment_status',
        'order_id',
        'payment_referenceno',
        'order_status',
        'total_amount',
        'shipping_amount',
        'subtotal_amount',
        'billing_firstname',
        'billing_lastname',
        'billing_email',
        'billing_mobile',
        'billing_address',
        'billing_country',
        'billing_state',
        'billing_city',
        'billing_pincode',
        'shipping_firstname',
        'shipping_lastname',
        'shipping_email',
        'shipping_mobile',
        'shipping_address',
        'shipping_country',
        'shipping_state',
        'shipping_city',
        'shipping_pincode',
        'date_time',
        'status'
    ];

    public function user()
    {
        return $this->belongsTo('App\Models\User', 'user_id');
    }

    public function supplier()
    {
        return $this->belongsTo('App\Models\User', 'supplier_id');
    }

    public function billingCountry()
    {
        return $this->belongsTo('App\Models\Contries', 'billing_country');
    }

    public function billingState()
    {
        return $this->belongsTo('App\Models\States', 'billing_state');
    }

    public function billingCity()
    {
        return $this->belongsTo('App\Models\Cities', 'billing_city');
    }

    public function billingPincode()
    {
        return $this->belongsTo('App\Models\Pincodes', 'billing_pincode');
    }

    public function shippingCountry()
    {
        return $this->belongsTo('App\Models\Contries', 'shipping_country');
    }

    public function shippingState()
    {
        return $this->belongsTo('App\Models\States', 'shipping_state');
    }

    public function shippingCity()
    {
        return $this->belongsTo('App\Models\Cities', 'shipping_city');
    }

    public function shippingPincode()
    {
        return $this->belongsTo('App\Models\Pincodes', 'shipping_pincode');
    }
}
