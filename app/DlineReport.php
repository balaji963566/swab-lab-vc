<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use \DateTimeInterface;

class DlineReport extends Model
{
    public $table = 'dline_reports';

    protected $dates = [
        'from_date',
        'to_date',
        'mailed_at',
        'created_at',
        'updated_at'
    ];

    protected $fillable = [
        'from_date',
        'to_date',
        'status',
        'mailed_at',
        'created_at',
        'updated_at'
    ];
}