<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Sanctum\HasApiTokens;

class onboardingInfo extends Model
{
    use HasApiTokens, HasFactory;
    protected $table = 'onboarding_info';
    protected $primaryKey = 'rocket_id';
    public $incrementing = false;

    protected $fillable = [
        'agency_name',
        'dba_name',
        'agent_name',
        'email',
        'phone',
        'address',
        'address1',
        'address2',
        'city',
        'state',
        'zip',
        'additional_states',
        'carriers',
        'aon', 
        'beyond',
        'dual',
        'flow',
        'neptune',
        'palomar',
        'sterling',
        'wright',
        'agent_npn',
        'agent_license',
        'agent_license_eff',
        'agent_license_exp',
        'agency_license',
        'agency_tax_id',
        'agency_type',
        'agency_logo'
    ];
}
