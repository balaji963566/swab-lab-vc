<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
//use Illuminate\Database\Eloquent\SoftDeletes;
use \DateTimeInterface;

class TestType extends Model
{
    public $table = 'test_type';

    protected $dates = [
        'created_at',
        'updated_at'
    ];

    protected $fillable = [
        'sample_id_prefix',
    ];
}
