<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use \DateTimeInterface;

class StateReport extends Model
{
    public $table = 'state_reports';

    protected $dates = [
        'from_date',
        'to_date',
        'mailed_at',
        'created_at',
        'updated_at'
    ];

    protected $fillable = [
        'facility_name',
        'from_date',
        'to_date',
        'status',
        'mailed_at',
        'created_at',
        'updated_at'
    ];
}