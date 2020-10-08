<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use \DateTimeInterface;

class Remark extends Model
{
    public $table = 'remarks';

    protected $dates = [
        'created_at',
        'updated_at'
    ];

    protected $fillable = [
        'inward_id',
        'remark',
        'created_at',
        'updated_at'
    ];
}