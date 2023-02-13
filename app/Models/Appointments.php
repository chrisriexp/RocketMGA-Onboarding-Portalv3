<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Sanctum\HasApiTokens;

class Appointments extends Model
{
    use HasApiTokens, HasFactory;
    protected $primaryKey = 'rocket_id';
    public $incrementing = false;

    protected $fillable = [
        'aon',
        'beyond',
        'dual',
        'flow',
        'neptune',
        'palomar',
        'sterling',
        'wright'
    ];
}
