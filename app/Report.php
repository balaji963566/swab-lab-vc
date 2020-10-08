<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use \DateTimeInterface;

class Report extends Model
{
    public $table = 'reports';

    protected $dates = [
        'created_at',
        'updated_at'
    ];

    protected $fillable = [
        'name',
        'file_path',
        'created_at',
        'updated_at'
    ];

    public function facility()
    {
		return $this->belongsTo('App\Facility');
    }

    public function sample_type()
    {
		return $this->belongsTo('App\SampleType');
    }
}