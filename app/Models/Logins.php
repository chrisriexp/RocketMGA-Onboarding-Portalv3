<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Sanctum\HasApiTokens;

class Logins extends Model
{
    use HasApiTokens, HasFactory;
    protected $primaryKey = 'rocket_id';
    public $incrementing = false;

    protected $fillable = [
        'AON Edge',
        'Beyond Flood',
        'Dual Flood',
        'Flow Flood',
        'Neptune',
        'Palomar',
        'Sterling',
        'Wright'
    ];
}
