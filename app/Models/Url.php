<?php

namespace App\Models;

use Jenssegers\Mongodb\Eloquent\Model;

class Url extends Model
{
    protected $connection = 'mongodb';
    protected $collection = 'urls';
    protected $dates = [
        'created_at',
        'expires_at'
    ];
}
