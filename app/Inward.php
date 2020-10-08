<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use \DateTimeInterface;

class Inward extends Model
{
    public $table = 'inwards';

    protected $dates = [
        'received_at',
        'testing_at',
        'tested_at',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected $fillable = [
        'facility_id',
        'sample_prefix',
        'sample_id',
        'patient_no',
        'name',
        'contact_no',
        'sample_type_id',
        'age',
        'sex',
        'address',
        'received_at',
        'testing_at',
        'tested_at',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }

    public function facility()
    {
		return $this->belongsTo('App\Facility');
    }

    public function sample_type()
    {
		return $this->belongsTo('App\SampleType');
    }

    public function rejected_reason()
    {
		return $this->belongsTo('App\SampleRejectedReason');
    }
}