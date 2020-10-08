<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use \DateTimeInterface;

class Facility extends Model
{
    use SoftDeletes;

    public $table = 'facilities';

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected $fillable = [
        'name',
        'short_name',
        'location',
        'facility_type',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }

    public function facility_emails()
    {
		return $this->hasMany('App\FacilityEmails');
        //return $this->belongsToMany(FacilityEmails::class,'facilities');
    }
}