<?php

namespace App\Http\Requests;

use App\Inward;
use Gate;
use Illuminate\Foundation\Http\FormRequest;
use Symfony\Component\HttpFoundation\Response;

class StoreInwardRequest extends FormRequest
{
    public function authorize()
    {
        //abort_if(Gate::denies('inward_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return true;
    }

    public function rules()
    {
        return true;
    }
}
