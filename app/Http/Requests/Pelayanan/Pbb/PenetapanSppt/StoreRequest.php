<?php

namespace App\Http\Requests\Pelayanan\Pbb\PenetapanSppt;

use App\Http\Requests\FormRequest;

class StoreRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'nop' => ['required', 'integer', 'max_digits:25'],
            'tahun' => 'required|integer',
        ];
    }

    protected function prepareForValidation()
    {
        $this->req->merge([
            'created_by' => authAttribute()['id']
        ]);
    }
}
