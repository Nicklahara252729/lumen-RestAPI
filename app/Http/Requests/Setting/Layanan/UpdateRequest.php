<?php

namespace App\Http\Requests\Setting\Layanan;

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
            'status'  => 'required|in:0,1',
        ];
    }
}
