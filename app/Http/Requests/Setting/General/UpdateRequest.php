<?php

namespace App\Http\Requests\Setting\General;

use App\Http\Requests\FormRequest;

class UpdateRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'category'  => 'required',
            'description'  => 'nullable',
        ];
    }
}
