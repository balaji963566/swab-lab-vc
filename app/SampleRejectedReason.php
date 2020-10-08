<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use \DateTimeInterface;

class SampleRejectedReason extends Model
{
    public $table = 'sample_rejected_reasons';

    protected $dates = [
        'created_at',
        'updated_at'
    ];

    protected $fillable = [
        'reason',
        'created_at',
        'updated_at'
    ];
}