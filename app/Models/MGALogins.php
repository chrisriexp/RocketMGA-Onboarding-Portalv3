<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MGALogins extends Model
{
    use HasFactory;

    protected $fillable = [
        'rocket_id',
        'name',
        'email',
        'role'
    ];
}
