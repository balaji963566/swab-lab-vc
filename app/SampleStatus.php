<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use \DateTimeInterface;

class SampleStatus extends Model
{
    public $table = 'sample_status';

    protected $dates = [
        'created_at',
        'updated_at'
    ];

    protected $fillable = [
        'status',
        'created_at',
        'updated_at'
    ];
}