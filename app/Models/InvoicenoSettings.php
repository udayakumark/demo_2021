<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InvoicenoSettings extends Model
{
    use HasFactory;
    protected $table      = 'invoiceno_settings';
    public $timestamps    = false;
    protected $primaryKey = 'id';

    protected $fillable = [
        'id',
        'type',
        'name',
		'invoice_no'
    ];

}
