<?php

namespace App\Http\Requests\Setting\Slider;

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
            'slider_name' => 'mimes:jpeg,png,jpg|max:3072',
            'title' => 'nullable',
            'description' => 'nullable'
        ];
    }
}
