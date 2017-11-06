<?php

namespace App\Models;

//use Illuminate\Database\Eloquent\Model;
//use Illuminate\Database\Eloquent\SoftDeletes;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Goods extends Authenticatable
{
    use Notifiable;
    protected $table ="goods";
}
