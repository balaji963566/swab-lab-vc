<?php

namespace App\Http\Requests;

use App\Facility;
use Gate;
use Illuminate\Foundation\Http\FormRequest;
use Symfony\Component\HttpFoundation\Response;

class StoreFacilityRequest extends FormRequest
{
    public function authorize()
    {
        abort_if(Gate::denies('facility_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return true;
    }

    public function rules()
    {
        return [
            'name' => ['required'],
            'short_name' => ['required'],
            'location' => ['required'],
        ];
    }
}
