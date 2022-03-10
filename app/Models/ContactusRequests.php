<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ContactusRequests extends Model
{
    use HasFactory;
    protected $table      = 'contactus_requests';
    public $timestamps    = false;
    protected $primaryKey = 'id';

    protected $fillable = [
        'id',
        'name',
        'email_id',
        'subject',
        'message',
        'date_time',
        'status'
    ];
}
