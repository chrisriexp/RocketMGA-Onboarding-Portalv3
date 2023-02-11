<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Sanctum\HasApiTokens;

class Approval extends Model
{
    use HasApiTokens, HasFactory;

    protected $primaryKey = 'rocket_id';
    public $incrementing = false;
    
    protected $fillable = [
        'eo_file',
        'eo_exp',
        'eo_limit',
        'license_file',
        'license',
        'agreement'
    ];
}
