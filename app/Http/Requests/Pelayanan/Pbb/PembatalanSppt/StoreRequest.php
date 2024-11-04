<?php

namespace App\Http\Requests\Pelayanan\Pbb\PembatalanSppt;

use App\Http\Requests\FormRequest;
use App\Rules\Nop;

class StoreRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'nop' => ['required', 'integer', 'max_digits:25', new Nop('sppt',$this->tahun)],
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
